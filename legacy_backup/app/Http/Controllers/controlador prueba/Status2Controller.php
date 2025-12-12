<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    // Mostrar una lista de los recursos
    public function index()
    {
        $statuses = Status::all();
        return view('statuses.index', compact('statuses'));
    }

    // Mostrar el formulario para crear un nuevo recurso
    public function create()
    {
        return view('statuses.create');
    }

    // Almacenar un nuevo recurso en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:255',
        ]);

        Status::create($request->all());
        return redirect()->route('statuses.index')
                         ->with('success', 'Status creado exitosamente.');
    }

    // Mostrar el recurso especificado
    public function show(Status $status)
    {
        return view('statuses.show', compact('status'));
    }

    // Mostrar el formulario para editar un recurso
    public function edit(Status $status)
    {
        return view('statuses.edit', compact('status'));
    }

    // Actualizar un recurso en la base de datos
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'status_name' => 'required|string|max:255',
        ]);

        $status->update($request->all());
        return redirect()->route('statuses.index')
                         ->with('success', 'Status actualizado exitosamente.');
    }

    // Eliminar un recurso de la base de datos
    public function destroy(Status $status)
    {
        $status->delete();
        return redirect()->route('statuses.index')
                         ->with('success', 'Status eliminado exitosamente.');
    }
}
