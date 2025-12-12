<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use App\Models\Estado;
use Illuminate\Http\Request;

class JornadaController extends Controller
{
    public function index()
    {
        $jornadas = Jornada::with('estado')->get();
        return view('jornadas.index', compact('jornadas'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('jornadas.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Jornada::create($request->all());

        return redirect()->route('jornadas.index')->with('success', 'Jornada creada exitosamente.');
    }

    public function show(Jornada $jornada)
    {
        return view('jornadas.show', compact('jornada'));
    }

    public function edit(Jornada $jornada)
    {
        $estados = Estado::all();
        return view('jornadas.edit', compact('jornada', 'estados'));
    }

    public function update(Request $request, Jornada $jornada)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $jornada->update($request->all());

        return redirect()->route('jornadas.index')->with('success', 'Jornada actualizada exitosamente.');
    }

    public function destroy(Jornada $jornada)
    {
        $jornada->delete();

        return redirect()->route('jornadas.index')->with('success', 'Jornada eliminada exitosamente.');
    }
}
