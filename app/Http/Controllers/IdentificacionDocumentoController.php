<?php

namespace App\Http\Controllers;

use App\Models\IdentificacionDocumento;
use Illuminate\Http\Request;

class IdentificacionDocumentoController extends Controller
{
    public function index()
    {
        $documentos = IdentificacionDocumento::paginate(10);
        return view('identificacion_documentos.index', compact('documentos'));
    }

    public function create()
    {
        return view('identificacion_documentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'max_digitos' => 'required|string|max:255',
        ]);

        IdentificacionDocumento::create($request->all());
        return redirect()->route('identificacion_documentos.index')->with('success', 'Documento de Identificación creado con éxito.');
    }

    public function show(IdentificacionDocumento $identificacionDocumento)
    {
        return view('identificacion_documentos.show', compact('identificacionDocumento'));
    }

    public function edit(IdentificacionDocumento $identificacionDocumento)
    {
        return view('identificacion_documentos.edit', compact('identificacionDocumento'));
    }

    public function update(Request $request, IdentificacionDocumento $identificacionDocumento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'max_digitos' => 'required|string|max:255',

        ]);

        $identificacionDocumento->update($request->all());
        return redirect()->route('identificacion_documentos.index')->with('success', 'Documento de Identificación actualizado con éxito.');
    }

    public function destroy(IdentificacionDocumento $identificacionDocumento)
    {
        $identificacionDocumento->delete();
        return redirect()->route('identificacion_documentos.index')->with('success', 'Documento de Identificación eliminado con éxito.');
    }
}
