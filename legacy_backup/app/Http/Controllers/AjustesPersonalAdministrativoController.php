<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrativo;
use App\Models\Persona;
use App\Models\Estado;
use App\Models\Cargo;

class AjustesPersonalAdministrativoController extends Controller
{
    
    public function index()
    {
        $administrativos = Administrativo::with(['persona', 'estado', 'cargo'])->get();
        return view('administrativos.ajustes.index', compact('administrativos'));
    }

    /**
     * Muestra el formulario para crear un nuevo administrativo.
     */
    public function create()
    {
        $personas = Persona::all();
        $estados = Estado::all();
        $cargos = Cargo::all();

        return view('administrativos.ajustes.create', compact('personas', 'estados', 'cargos'));
    }

    /**
     * Guarda un nuevo administrativo en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'estado_id' => 'required|exists:tb_estados,id',
            'cargo_id' => 'required|exists:cargos,id',
            'fotografia_administrativo' => 'nullable|image|max:2048',
        ]);

        $datos = $request->all();

        if ($request->hasFile('fotografia_administrativo')) {
            $datos['fotografia_administrativo'] = $request->file('fotografia_administrativo')->store('administrativos', 'public');
        }

        Administrativo::create($datos);

        return redirect()->route('ajuste-administrativos.index')->with('success', 'Administrativo creado correctamente.');
    }

    /**
     * Muestra los detalles de un administrativo específico.
     */
    public function show($id)
    {
        $administrativo = Administrativo::with(['persona', 'estado', 'cargo'])->findOrFail($id);
        return view('administrativos.ajustes.show', compact('administrativo'));
    }

    /**
     * Muestra el formulario para editar un administrativo.
     */
    public function edit($id)
    {
        $administrativo = Administrativo::findOrFail($id);
        $personas = Persona::all();
        $estados = Estado::all();
        $cargos = Cargo::all();

        return view('administrativos.ajustes.edit', compact('administrativo', 'personas', 'estados', 'cargos'));
    }

    /**
     * Actualiza un administrativo en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $administrativo = Administrativo::findOrFail($id);

        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'estado_id' => 'required|exists:tb_estados,id',
            'cargo_id' => 'required|exists:cargos,id',
            'fotografia_administrativo' => 'nullable|image|max:2048',
        ]);

        $datos = $request->all();

        if ($request->hasFile('fotografia_administrativo')) {
            $datos['fotografia_administrativo'] = $request->file('fotografia_administrativo')->store('administrativos', 'public');
        }

        $administrativo->update($datos);

        return redirect()->route('ajuste-administrativos.index')->with('success', 'Administrativo actualizado correctamente.');
    }

    /**
     * Elimina un administrativo de la base de datos.
     */
    public function destroy($id)
    {
        $administrativo = Administrativo::findOrFail($id);
        $administrativo->delete();

        return redirect()->route('ajuste-administrativos.index')->with('success', 'Administrativo eliminado correctamente.');
    }
}
