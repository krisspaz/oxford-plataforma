<?php

namespace App\Http\Controllers;

use App\Models\Madre;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\IdentificacionDocumento;
use Illuminate\Http\Request;

class MadreController extends Controller
{
    public function index()
    {
        $madres = Madre::all();
        return view('madres.index', compact('madres'));
    }

    public function create()
    {
        $departamentos = Departamento::all();
        $identificacionDocumentos = IdentificacionDocumento::all();
        return view('madres.create', compact('departamentos', 'identificacionDocumentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'identificacion_documentos_id' => 'required',
            'num_documento' => 'required',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'required',
            'telefono' => 'required',
            'municipio_id' => 'required',
            'direccion' => 'required',
        ]);

        Madre::create($request->all());

        return redirect()->route('madres.index')->with('success', 'Madre creada exitosamente.');
    }

    public function show(Madre $madre)
    {
        return view('madres.show', compact('madre'));
    }

    public function edit(Madre $madre)
    {
        $departamentos = Departamento::all();
        $municipios = Municipio::where('departamento_id', $madre->municipio->departamento_id)->get();
        $identificacionDocumentos = IdentificacionDocumento::all();
        return view('madres.edit', compact('madre', 'departamentos', 'municipios', 'identificacionDocumentos'));
    }

    public function update(Request $request, Madre $madre)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'identificacion_documentos_id' => 'required',
            'num_documento' => 'required',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'required',
            'telefono' => 'required',
            'municipio_id' => 'required',
            'direccion' => 'required',
        ]);

        $madre->update($request->all());

        return redirect()->route('madres.index')->with('success', 'Madre actualizada exitosamente.');
    }

    public function destroy(Madre $madre)
    {
        $madre->delete();
        return redirect()->route('madres.index')->with('success', 'Madre eliminada exitosamente.');
    }

    public function getMunicipios($departamento_id)
    {
        $municipios = Municipio::where('departamento_id', $departamento_id)->get();
        return response()->json($municipios);
    }
}
