<?php

namespace App\Http\Controllers;

use App\Models\Padre;
use App\Models\IdentificacionDocumento;
use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Http\Request;

class PadreController extends Controller
{
    public function index()
    {
        $padres = Padre::with('identificacionDocumento', 'municipio')->paginate(10);
        return view('padres.index', compact('padres'));
    }

    public function create()
    {
        $identificacionDocumentos = IdentificacionDocumento::all();
        $departamentos = Departamento::all();
        return view('padres.create', compact('identificacionDocumentos', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'identificacion_documentos_id' => 'required|exists:tb_identificacion_documentos,id',
            'num_documento' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:255',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'nullable|string',
        ]);

        Padre::create($request->all());
        return redirect()->route('padres.index')->with('success', 'Padre creado con éxito.');
    }

    public function show(Padre $padre)
    {
        return view('padres.show', compact('padre'));
    }

    public function edit(Padre $padre)
    {
        $identificacionDocumentos = IdentificacionDocumento::all();
        $departamentos = Departamento::all();
        return view('padres.edit', compact('padre', 'identificacionDocumentos', 'departamentos'));
    }

    public function update(Request $request, Padre $padre)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'identificacion_documentos_id' => 'required|exists:tb_identificacion_documentos,id',
            'num_documento' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:255',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'nullable|string',
        ]);

        $padre->update($request->all());
        return redirect()->route('padres.index')->with('success', 'Padre actualizado con éxito.');
    }

    public function destroy(Padre $padre)
    {
        $padre->delete();
        return redirect()->route('padres.index')->with('success', 'Padre eliminado con éxito.');
    }

    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)->get();
        return response()->json($municipios);
    }
}
