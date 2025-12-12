<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Matriculacion;
use App\Models\Estudiante;
use App\Models\Paquete;
use App\Models\Estado;

class MatriculacionController extends Controller
{
    /**
     * Mostrar una lista de matriculaciones.
     */
    public function index()
    {
        $matriculaciones = Matriculacion::with(['estudiante', 'paquete', 'estado', 'cmsUser'])->get();
        return view('matriculaciones.index', compact('matriculaciones'));
    }

    /**
     * Mostrar el formulario para crear una nueva matriculación.
     */
    public function create()
    {
        $estudiantes = Estudiante::all();
        $paquetes = Paquete::all();
        $estados = Estado::all();
        return view('matriculaciones.create', compact('estudiantes', 'paquetes', 'estados'));
    }

    /**
     * Guardar una nueva matriculación en la base de datos.
     */
    public function store(Request $request)
    {



        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'paquete_id' => 'required|exists:paquetes,id',
            'fecha_inscripcion' => 'required|date',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Matriculacion::create([
            'estudiante_id' => $request->estudiante_id,
            'cgshges_id' => $request->cgshges_id,
            'paquete_id' => $request->paquete_id,
            'fecha_inscripcion' => $request->fecha_inscripcion,
            'estado_id' => $request->estado_id,
            'cms_users_id' => auth()->id(),
        ]);

        return redirect()->route('matriculaciones.index')->with('success', 'Matriculación creada exitosamente.');
    }

    /**
     * Mostrar los detalles de una matriculación específica.
     */
    public function show(Matriculacion $matriculacion)
    {
        return view('matriculaciones.show', compact('matriculacion'));
    }

    /**
     * Mostrar el formulario para editar una matriculación existente.
     */
    public function edit(Matriculacion $matriculacion)
    {
        $estudiantes = Estudiante::all();
        $paquetes = Paquete::all();
        $estados = Estado::all();
        return view('matriculaciones.edit', compact('matriculacion', 'estudiantes', 'paquetes', 'estados'));
    }

    /**
     * Actualizar una matriculación en la base de datos.
     */
    public function update(Request $request, Matriculacion $matriculacion)
    {


        $request->validate([
            'estudiante_id' => 'exists:estudiantes,id',
            'paquete_id' => 'exists:paquetes,id',
            'fecha_inscripcion' => 'date',
            'estado_id' => 'exists:tb_estados,id',
        ]);

        $matriculacion->update($request->all());

        return redirect()->route('matriculaciones.index')->with('success', 'Matriculación actualizada exitosamente.');
    }

    /**
     * Eliminar una matriculación de la base de datos.
     */
    public function destroy(Matriculacion $matriculacion)
    {
        $matriculacion->delete();
        return redirect()->route('matriculaciones.index')->with('success', 'Matriculación eliminada exitosamente.');
    }
}
