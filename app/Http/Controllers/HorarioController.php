<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Estado;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with('estado')->get();
        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('horarios.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inicio' => 'required|date_format:H:i',
            'fin' => 'required|date_format:H:i',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Horario::create($request->all());

        return redirect()->route('horarios.index')->with('success', 'Horario creado exitosamente.');
    }

    public function show(Horario $horario)
    {
        return view('horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        $estados = Estado::all();
        return view('horarios.edit', compact('horario', 'estados'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'inicio' => 'required|date_format:H:i',
            'fin' => 'required|date_format:H:i',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $horario->update($request->all());

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado exitosamente.');
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();

        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
}
