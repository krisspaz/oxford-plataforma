<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Cgshge;
use App\Models\Nivel;
use App\Models\Estado;
use App\Models\Persona;
use Illuminate\Http\Request;

class AjustesAsignacionController extends Controller
{
    /**
     * Mostrar una lista de todos los estudiantes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener todos los estudiantes con las relaciones necesarias
       
        $estudiantes = Estudiante::with(['persona', 'nivel', 'estado', 'inscripciones'])->get();

        return view('ajustes_asignacion.index', compact('estudiantes'));
    }

    /**
     * Mostrar el formulario para crear un nuevo estudiante.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $niveles = Nivel::all(); // Obtener niveles
        $cgshges = Cgshge::all(); // Obtener Cgshges
        $estados = Estado::all(); // Obtener estados
        $personas = Persona::all();

        return view('ajustes_asignacion.create', compact('niveles', 'cgshges', 'estados', 'personas'));
    }

    /**
     * Almacenar un nuevo estudiante en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'fotografia_estudiante' => 'nullable|image|mimes:jpg,jpeg,png',
            'persona_id' => 'required|exists:personas,id',
            'cgshges_id' => 'required|exists:cgshges,id',
            'estado_id' => 'required|exists:estados,id',
            'nivel_id' => 'required|exists:niveles,id',
        ]);

        // Crear el estudiante
        $estudiante = Estudiante::create([
            'fotografia_estudiante' => $request->file('fotografia_estudiante') ? $request->file('fotografia_estudiante')->store('fotografias_estudiantes') : null,
            'persona_id' => $request->persona_id,
            'cgshges_id' => $request->cgshges_id,
            'estado_id' => $request->estado_id,
            'nivel_id' => $request->nivel_id,
        ]);

        return redirect()->route('ajustes_asignacion.index')->with('success', 'Estudiante creado correctamente.');
    }

    /**
     * Mostrar el formulario para editar un estudiante existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $niveles = Nivel::all();
        $cgshges = Cgshge::all();
        $estados = Estado::all();
        $personas = Persona::all();

        return view('ajustes_asignacion.edit', compact('estudiante', 'niveles', 'cgshges', 'personas', 'estados'));
    }

    /**
     * Actualizar los datos de un estudiante existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validación de los campos
        $request->validate([
            'fotografia_estudiante' => 'nullable|image|mimes:jpg,jpeg,png',
            'persona_id' => 'required|exists:personas,id',
            'cgshges_id' => 'required|exists:cgshges,id',
            'estado_id' => 'required|exists:estados,id',
            'nivel_id' => 'required|exists:niveles,id',
        ]);

        // Encontrar al estudiante
        $estudiante = Estudiante::findOrFail($id);

        // Actualizar la información
        $estudiante->update([
            'fotografia_estudiante' => $request->file('fotografia_estudiante') ? $request->file('fotografia_estudiante')->store('fotografias_estudiantes') : $estudiante->fotografia_estudiante,
            'persona_id' => $request->persona_id,
            'cgshges_id' => $request->cgshges_id,
            'estado_id' => $request->estado_id,
            'nivel_id' => $request->nivel_id,
        ]);

        return redirect()->route('ajustes_asignacion.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    /**
     * Eliminar un estudiante.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $estudiante->delete();

        return redirect()->route('ajustes_asignacion.index')->with('success', 'Estudiante eliminado correctamente.');
    }
}
