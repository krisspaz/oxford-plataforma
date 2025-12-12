<?php

namespace App\Http\Controllers;

use App\Models\Telefono;
use Illuminate\Http\Request;

class TelefonoController extends Controller
{
    public function index()
    {
        $telefonos = Telefono::all();
        return view('telefonos.index', compact('telefonos'));
    }

    public function create()
    {
        
        return view('telefonos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'telefono' => 'required|string',
            'estado_id' => 'exists:tb_estados,id',
        ]);

        Telefono::create($request->all());

        return redirect()->route('telefonos.index')->with('success', 'Teléfono creado correctamente.');
    }

    public function show(Telefono $telefono)
    {
        return view('telefonos.show', compact('telefono'));
    }

    public function edit(Telefono $telefono)
    {
        
        return view('telefonos.edit', compact('telefono'));
    }

    public function update(Request $request, Telefono $telefono)
    {
        $request->validate([
            'telefono' => 'required|string|max:255',
            
        ]);

        $telefono->update($request->all());

        return redirect()->route('telefonos.index')->with('success', 'Teléfono actualizado correctamente.');
    }

    public function destroy(Telefono $telefono)
    {
        $telefono->delete();
        return redirect()->route('telefonos.index')->with('success', 'Teléfono eliminado correctamente.');
    }
}
