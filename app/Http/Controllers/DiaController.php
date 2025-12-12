<?php

namespace App\Http\Controllers;

use App\Models\Dia;
use App\Models\Estado;
use Illuminate\Http\Request;

class DiaController extends Controller
{
    public function index()
    {
        $dias = Dia::with('estado')->get();
        return view('dias.index', compact('dias'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('dias.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Dia::create($request->all());

        return redirect()->route('dias.index')->with('success', 'Día creado exitosamente.');
    }

    public function show(Dia $dia)
    {
        return view('dias.show', compact('dia'));
    }

    public function edit(Dia $dia)
    {
        $estados = Estado::all();
        return view('dias.edit', compact('dia', 'estados'));
    }

    public function update(Request $request, Dia $dia)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $dia->update($request->all());

        return redirect()->route('dias.index')->with('success', 'Día actualizado exitosamente.');
    }

    public function destroy(Dia $dia)
    {
        $dia->delete();

        return redirect()->route('dias.index')->with('success', 'Día eliminado exitosamente.');
    }
}
