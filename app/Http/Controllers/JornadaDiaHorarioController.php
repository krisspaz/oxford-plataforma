<?php

namespace App\Http\Controllers;

use App\Models\JornadaDiaHorario;
use App\Models\Jornada;
use App\Models\Dia;
use App\Models\Horario;
use App\Models\Estado;
use Illuminate\Http\Request;

class JornadaDiaHorarioController extends Controller
{
    public function index()
    {
        $jornadaDiaHorarios = JornadaDiaHorario::with(['jornada', 'dia', 'horario', 'estado'])->get();
        return view('jornada_dia_horarios.index', compact('jornadaDiaHorarios'));
    }

    public function create()
    {
        $jornadas = Jornada::all();
        $dias = Dia::all();
        $horarios = Horario::all();
        $estados = Estado::all();
        return view('jornada_dia_horarios.create', compact('jornadas', 'dias', 'horarios', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jornada_id' => 'required|exists:tb_jornadas,id',
            'dia_id' => 'required|exists:tb_dias,id',
            'horario_id' => 'required|exists:tb_horarios,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        JornadaDiaHorario::create($request->all());

        return redirect()->route('jornada-dia-horarios.index')->with('success', 'Jornada-Dia-Horario creado exitosamente.');
    }

    public function show(JornadaDiaHorario $jornadaDiaHorario)
    {
        return view('jornada_dia_horarios.show', compact('jornadaDiaHorario'));
    }

    public function edit(JornadaDiaHorario $jornadaDiaHorario)
    {
        $jornadas = Jornada::all();
        $dias = Dia::all();
        $horarios = Horario::all();
        $estados = Estado::all();
        return view('jornada_dia_horarios.edit', compact('jornadaDiaHorario', 'jornadas', 'dias', 'horarios', 'estados'));
    }

    public function update(Request $request, JornadaDiaHorario $jornadaDiaHorario)
    {
        $request->validate([
            'jornada_id' => 'required|exists:tb_jornadas,id',
            'dia_id' => 'required|exists:tb_dias,id',
            'horario_id' => 'required|exists:tb_horarios,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $jornadaDiaHorario->update($request->all());

        return redirect()->route('jornada-dia-horarios.index')->with('success', 'Jornada-Dia-Horario actualizado exitosamente.');
    }

    public function destroy(JornadaDiaHorario $jornadaDiaHorario)
    {
        $jornadaDiaHorario->delete();

        return redirect()->route('jornada-dia-horarios.index')->with('success', 'Jornada-Dia-Horario eliminado exitosamente.');
    }
}
