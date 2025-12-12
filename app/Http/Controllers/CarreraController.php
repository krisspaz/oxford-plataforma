<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Estado;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::with('estado')->get();
        return view('carreras.index', compact('carreras'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('carreras.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Carrera::create($request->all());

        return redirect()->route('carreras.index')->with('success', 'Carrera creada exitosamente.');
    }

    public function show(Carrera $carrera)
    {
        return view('carreras.show', compact('carrera'));
    }

    public function edit(Carrera $carrera)
    {
        $estados = Estado::all();
        return view('carreras.edit', compact('carrera', 'estados'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $carrera->update($request->all());

        return redirect()->route('carreras.index')->with('success', 'Carrera actualizada exitosamente.');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->delete();

        return redirect()->route('carreras.index')->with('success', 'Carrera eliminada exitosamente.');
    }
}
