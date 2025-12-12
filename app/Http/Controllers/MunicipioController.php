<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Departamento;
use App\Models\Estado;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::with('estado', 'departamento')->get();
        return view('municipios.index', compact('municipios'));
    }

    public function create()
    {
        $estados = Estado::all();
        $departamentos = Departamento::all();
        return view('municipios.create', compact('estados', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'municipio' => 'required|string|max:255',
            'departamento_id' => 'required|exists:tb_departamentos,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Municipio::create($request->all());

        return redirect()->route('municipios.index')->with('success', 'Municipio creado exitosamente.');
    }

    public function show(Municipio $municipio)
    {
        return view('municipios.show', compact('municipio'));
    }

    public function edit(Municipio $municipio)
    {
        $estados = Estado::all();
        $departamentos = Departamento::all();
        return view('municipios.edit', compact('municipio', 'estados', 'departamentos'));
    }

    public function update(Request $request, Municipio $municipio)
    {
        $request->validate([
            'municipio' => 'required|string|max:255',
            'departamento_id' => 'required|exists:tb_departamentos,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $municipio->update($request->all());

        return redirect()->route('municipios.index')->with('success', 'Municipio actualizado exitosamente.');
    }

    public function destroy(Municipio $municipio)
    {
        $municipio->delete();

        return redirect()->route('municipios.index')->with('success', 'Municipio eliminado exitosamente.');
    }

    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)->get();
        return response()->json(['municipios' => $municipios]);
    }


    public function getCodigoPostal($municipioId)
    {
        $municipio = Municipio::find($municipioId);
    
        if ($municipio) {
            return response()->json(['codigopostal' => $municipio->codigo_postal]);
        } else {
            return response()->json(['codigopostal' => null], 404);
        }
    }

}
