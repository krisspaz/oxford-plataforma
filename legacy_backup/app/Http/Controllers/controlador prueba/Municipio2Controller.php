<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Departamento;
use App\Models\Status;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    // Mostrar una lista de los recursos
    public function index()
    {
        $municipios = Municipio::with(['departamento', 'status'])->get();
        return view('municipios.index', compact('municipios'));
    }

    // Mostrar el formulario para crear un nuevo recurso
    public function create()
    {
        $departamentos = Departamento::all();
        $statuses = Status::all();
        return view('municipios.create', compact('departamentos', 'statuses'));
    }

    // Almacenar un nuevo recurso en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'municipio' => 'required|string|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
            'status_id' => 'required|exists:status,id',
        ]);

        Municipio::create($request->all());
        return redirect()->route('municipios.index')
                         ->with('success', 'Municipio creado exitosamente.');
    }

    // Mostrar el recurso especificado
    public function show(Municipio $municipio)
    {
        return view('municipios.show', compact('municipio'));
    }

    // Mostrar el formulario para editar un recurso
    public function edit(Municipio $municipio)
    {
        $departamentos = Departamento::all();
        $statuses = Status::all();
        return view('municipios.edit', compact('municipio', 'departamentos', 'statuses'));
    }

    // Actualizar un recurso en la base de datos
    public function update(Request $request, Municipio $municipio)
    {
        $request->validate([
            'municipio' => 'required|string|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
            'status_id' => 'required|exists:status,id',
        ]);

        $municipio->update($request->all());
        return redirect()->route('municipios.index')
                         ->with('success', 'Municipio actualizado exitosamente.');
    }

    // Eliminar un recurso de la base de datos
    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return redirect()->route('municipios.index')
                         ->with('success', 'Municipio eliminado exitosamente.');
    }
}
