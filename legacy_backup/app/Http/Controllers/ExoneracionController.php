<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class ExoneracionController extends Controller
{
    // Mostrar cuotas pendientes del estudiante
    public function mostrarCuotas($estudianteId)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($estudianteId);



        $cuotas = Cuota::whereHas('convenio', function ($q) use ($estudianteId) {
            $q->whereHas('inscripcion', function ($q2) use ($estudianteId) {
                $q2->where('estudiante_id', $estudianteId);
            });
        })
    ->where('estado', 'pendiente')
    ->where(function ($q) {
        $q->whereNull('baja')
          ->orWhere('baja', 'no');
    })
    ->orderBy('fecha_vencimiento', 'asc')
    ->get();

        return view('cuotas.exonerar', compact('estudiante', 'cuotas'));
    }

    public function mostrarCuotasExoneradas($estudianteId)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($estudianteId);



        $cuotas = Cuota::whereHas('convenio.inscripcion', function ($q) use ($estudianteId) {
            $q->where('estudiante_id', $estudianteId);
        })
            ->where('baja', 'si')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('cuotas.recharexonerados', compact('estudiante', 'cuotas'));
    }

    public function mostrarListaCuotasExoneradas($estudianteId)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($estudianteId);



        $cuotas = Cuota::whereHas('convenio.inscripcion', function ($q) use ($estudianteId) {
            $q->where('estudiante_id', $estudianteId);
        })
            ->where('baja', 'si')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('cuotas.procesarexoneracion', compact('estudiante', 'cuotas'));
    }

    // Exonerar cuotas seleccionadas


    public function procesarexoneracion(Request $request)
    {
        $request->validate([
            'cuotas' => 'required|array',
            'cuotas.*' => 'exists:cuotas,id',
            'comentario' => 'required|string|min:5',
            'estado' => 'required|string'

        ]);

        $cuotas = Cuota::whereIn('id', $request->cuotas)->get();

        foreach ($cuotas as $cuota) {
            $cuota->estado = $request->estado;
            $cuota->baja = 'Autorizado';
            $cuota->comentario = $request->comentario;
            $cuota->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Las cuotas fueron exoneradas correctamente.');
    }

    public function solicitudexonerarCuotas(Request $request)
    {
        $request->validate([
            'cuotas' => 'required|array',
            'cuotas.*' => 'exists:cuotas,id',
            'comentario' => 'required|string|min:5'

        ]);

        $cuotas = Cuota::whereIn('id', $request->cuotas)->get();

        foreach ($cuotas as $cuota) {
            $cuota->estado = 'pendiente';
            $cuota->baja = 'si';
            $cuota->comentario = $request->comentario;
            $cuota->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Las cuotas fueron exoneradas correctamente por baja voluntaria.');
    }


    // Exonerar cuotas seleccionadas
    public function rechazarexonerarCuotas(Request $request)
    {
        $request->validate([
            'cuotas' => 'required|array',
            'cuotas.*' => 'exists:cuotas,id',
            'comentario' => 'required|string|min:5'
        ]);

        $cuotas = Cuota::whereIn('id', $request->cuotas)->get();

        foreach ($cuotas as $cuota) {
            $cuota->estado = 'pendiente';
            $cuota->baja = 'no';
            $cuota->comentario = $request->comentario;
            $cuota->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Las cuotas fueron rechazadas correctamente.');
    }
}
