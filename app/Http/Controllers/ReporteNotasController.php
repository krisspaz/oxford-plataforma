<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuadroNota;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Curso;
use App\Models\Jornada;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Bimestre;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteNotasController extends Controller
{
    //

    public function reporteBoleta($estudiante_id, $tipo_promedio, $ciclo_escolar)
    {


        $estudiante = Estudiante::with('persona')->findOrFail($estudiante_id);
        $añoActual = now()->year;

        // Agrupar por materia y bimestre
        $notas = CuadroNota::where('estudiante_id', $estudiante_id)
            ->with('materia.gestionMateria')
            ->where('ciclo_escolar', $ciclo_escolar)
            ->get()
            ->groupBy('materia_id');



        return view('reportes.boletanotas', compact('estudiante', 'notas', 'tipo_promedio', 'ciclo_escolar'));
    }


    public function exportarPDF($estudiante_id, Request $request)
    {


        $tipo_promedio = $request->input('tipo_promedio');
        $ciclo_escolar = $request->input('ciclo_escolar');

        $estudiante = Estudiante::with('persona')->findOrFail($estudiante_id);

        $notas = CuadroNota::where('estudiante_id', $estudiante_id)
         ->where('ciclo_escolar', $ciclo_escolar) // <-- filtramos por ciclo escolar activo
         ->with('materia.gestionMateria')
         ->get()
         ->groupBy('materia_id');

        $pdf = \PDF::loadView('reportes.boleta_pdf', compact('estudiante', 'notas', 'tipo_promedio'));

        return $pdf->download("boleta_{$estudiante->persona->nombres}_{$estudiante->persona->apellidos}.pdf");
    }


    public function reporteBoletasPorFiltros(Request $request)
    {
        // Validamos
        $request->validate([
            'grado_id'   => 'required|exists:tb_grados,id',
            'curso_id'   => 'required|exists:cursos,id',
            'jornada_id' => 'required|exists:tb_jornadas,id',
            'gestion_id' => 'required|exists:gestionesacademicas,id',
        ]);

        // Obtener ciclo escolar desde gestión
        $gestion = Gestion::find($request->gestion_id);
        $ciclo_escolar = $gestion->ciclo_escolar;

        $tipo_promedio = $request->promedio;

        // 1️⃣ Buscar estudiantes por su inscripción (matriculación)
        $estudiantes = Estudiante::whereHas('inscripciones', function ($query) use ($request) {
            $query->whereHas('cgshges', function ($q) use ($request) {
                $q->where('grado_id', $request->grado_id)
                  ->where('curso_id', $request->curso_id)
                  ->where('jornada_id', $request->jornada_id)
                  ->where('gestion_id', $request->gestion_id);
            });
        })
        ->with(['persona', 'inscripciones.cgshges'])
        ->get();

        if ($estudiantes->isEmpty()) {
            return back()->with('error', 'No se encontraron estudiantes para los filtros seleccionados.');
        }

        // 2️⃣ Obtener notas filtradas por ciclo escolar
        $notas = [];

        foreach ($estudiantes as $estudiante) {
            $notas[$estudiante->id] = CuadroNota::where('estudiante_id', $estudiante->id)
                ->where('ciclo_escolar', $ciclo_escolar)
                ->with('materia.gestionMateria')
                ->get()
                ->groupBy('materia_id');
        }

        // 3️⃣ Generar PDF
        $pdf = \PDF::loadView('reportes.mboleta_pdf', compact(
            'estudiantes',
            'notas',
            'tipo_promedio',
            'ciclo_escolar'
        ));

        return $pdf->download('boletas_calificaciones.pdf');
    }




    public function mostrarFormularioFiltros()
    {
        // Obtener los grados, cursos, jornadas y gestiones disponibles
        $grados = Grado::all();
        $cursos = Curso::all();
        $jornadas = Jornada::all();
        $gestiones = Gestion::all();

        // Retornar la vista con los datos necesarios
        return view('reportes.filtro_boletas', compact('grados', 'cursos', 'jornadas', 'gestiones'));
    }



}
