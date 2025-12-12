<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use App\Models\Estado;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index()
    {
        $niveles = Nivel::with('estado')->get();
        return view('niveles.index', compact('niveles'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('niveles.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Nivel::create($request->all());

        return redirect()->route('niveles.index')->with('success', 'Nivel creado exitosamente.');
    }

    public function show(Nivel $nivele)
    {
        return view('niveles.show', compact('nivele'));
    }

    public function edit(Nivel $nivele)
    {
        $estados = Estado::all();
        return view('niveles.edit', compact('nivele', 'estados'));
    }

    public function update(Request $request, Nivel $nivele)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $nivele->update($request->all());

        return redirect()->route('niveles.index')->with('success', 'Nivel actualizado exitosamente.');
    }

    public function destroy(Nivel $nivele)
    {
        $nivele->delete();

        return redirect()->route('niveles.index')->with('success', 'Nivel eliminado exitosamente.');
    }
}
