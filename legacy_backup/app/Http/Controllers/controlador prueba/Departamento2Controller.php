<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Status;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    // Mostrar una lista de los recursos
    public function index()
    {
        $departamentos = Departamento::with('status')->get();
        return view('departamentos.index', compact('departamentos'));
    }

    // Mostrar el formulario para crear un nuevo recurso
    public function create()
    {
        $statuses = Status::all();
        return view('departamentos.create', compact('statuses'));
    }

    // Almacenar un nuevo recurso en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'status_id' => 'required|exists:status,id',
        ]);

        Departamento::create($request->all());
        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento creado exitosamente.');
    }

    // Mostrar el recurso especificado
    public function show(Departamento $departamento)
    {
        return view('departamentos.show', compact('departamento'));
    }

    // Mostrar el formulario para editar un recurso
    public function edit(Departamento $departamento)
    {
        $statuses = Status::all();
        return view('departamentos.edit', compact('departamento', 'statuses'));
    }

    // Actualizar un recurso en la base de datos
    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'status_id' => 'required|exists:status,id',
        ]);

        $departamento->update($request->all());
        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento actualizado exitosamente.');
    }

    // Eliminar un recurso de la base de datos
    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento eliminado exitosamente.');
    }
}
