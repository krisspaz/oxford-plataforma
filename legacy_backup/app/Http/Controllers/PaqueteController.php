<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\Curso;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaqueteController extends Controller
{
    public function index()
    {
        

        $paquetes = Paquete::with(['cursos', 'estado'])->get();
        return view('paquetes.index', compact('paquetes'));
    }

    public function create()
    {
        $cursos = DB::table('cursos')
         ->distinct()
         ->join('pv_cgshges', 'cursos.id', '=', 'pv_cgshges.curso_id')
         ->select('cursos.id as curso_id', 'cursos.curso')
         ->limit(25)
         ->get();
        //$cursos = Curso::all();
        $estados = Estado::all();
        return view('paquetes.create', compact('cursos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'cursos' => 'required|array',  // Asegúrate de que se haya seleccionado al menos un curso
            'cursos.*' => 'exists:cursos,id',  // Valida que los IDs de cursos sean válidos
            'estado_id' => 'required|exists:tb_estados,id',
        ]);
    
        // Crear el paquete
        $paquete = Paquete::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'estado_id' => $request->estado_id,
        ]);
    
        // Asociar los cursos seleccionados con el paquete (relación muchos a muchos)
        $paquete->cursos()->sync($request->cursos);  // sync() agrega los cursos seleccionados
    
        return redirect()->route('paquetes.index')->with('success', 'Paquete creado exitosamente.');
    }
    

    

    public function show(Paquete $paquete)
    {
        $paquete->load('cursos', 'estado'); // Carga las relaciones para optimizar la consulta
        return view('paquetes.show', compact('paquete'));
    }


    
    public function edit(Paquete $paquete)
    {
        // $cursos = Curso::all();
        $cursos = DB::table('cursos')
            ->distinct()
            ->join('pv_cgshges', 'cursos.id', '=', 'pv_cgshges.curso_id')
            ->select('cursos.id as curso_id', 'cursos.curso')
            ->limit(25)
            ->get();
    
        $estados = Estado::all();
        $cursosSeleccionados = $paquete->cursos->pluck('id')->toArray();

        return view('paquetes.edit', compact('paquete', 'cursos', 'estados', 'cursosSeleccionados'));
    }

    
    public function update(Request $request, Paquete $paquete)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'cursos' => 'required|array', // Asegúrate de que se seleccione al menos un curso
            'cursos.*' => 'exists:cursos,id', // Validar IDs de cursos
            'estado_id' => 'required|exists:tb_estados,id',
        ]);
    
        // Actualizar los datos del paquete
        $paquete->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'estado_id' => $request->estado_id,
        ]);
    
        // Sincronizar los cursos seleccionados
        $paquete->cursos()->sync($request->cursos);
    
        return redirect()->route('paquetes.index')->with('success', 'Paquete actualizado exitosamente.');
    }
    

    public function destroy(Paquete $paquete)
    {
        $paquete->delete();

        return redirect()->route('paquetes.index')->with('success', 'Paquete eliminado exitosamente.');
    }

    public function recalcularPrecio($id)
    {
        $paquete = Paquete::with('detalles')->find($id);
    
        if (!$paquete) {
            return redirect()->back()->with('error', 'Paquete no encontrado');
        }
    
        $precioTotal = 0;
    
        foreach ($paquete->detalles as $detalle) {
            if (strtolower($detalle->nombre) == 'mensualidad') {
                $precioTotal += $detalle->precio * 10;
            } else {
                $precioTotal += $detalle->precio;
            }
        }
    
        $paquete->precio = $precioTotal;
        $paquete->save();
    
        return redirect()->back()->with('success', 'Precio recalculado correctamente');
    }
    

}
