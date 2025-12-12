<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\GradoCarrera;
use App\Models\Grado;
use App\Models\Carrera;
use App\Models\Estado;
use Illuminate\Http\Request;

class GradoCarreraController extends Controller
{
    public function index()
    {
        $gradoCarreras = GradoCarrera::with(['grado', 'carrera', 'estado'])->get();
        return view('grado_carreras.index', compact('gradoCarreras'));
    }

    public function create()
    {
        $grados = Grado::all();
        $carreras = Carrera::all();
        $estados = Estado::all();
        return view('grado_carreras.create', compact('grados', 'carreras', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grado_id' => 'required|exists:tb_grados,id',
            'carrera_id' => 'required|exists:tb_carreras,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        GradoCarrera::create($request->all());

        return redirect()->route('grado-carreras.index')->with('success', 'Grado-Carrera creado exitosamente.');
    }

    public function show(GradoCarrera $gradoCarrera)
    {
        return view('grado_carreras.show', compact('gradoCarrera'));
    }

    public function edit(GradoCarrera $gradoCarrera)
    {
        $grados = Grado::all();
        $carreras = Carrera::all();
        $estados = Estado::all();
        return view('grado_carreras.edit', compact('gradoCarrera', 'grados', 'carreras', 'estados'));
    }

    public function update(Request $request, GradoCarrera $gradoCarrera)
    {
        $request->validate([
            'grado_id' => 'required|exists:tb_grados,id',
            'carrera_id' => 'required|exists:tb_carreras,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $gradoCarrera->update($request->all());

        return redirect()->route('grado-carreras.index')->with('success', 'Grado-Carrera actualizado exitosamente.');
    }

    public function destroy(GradoCarrera $gradoCarrera)
    {
        $gradoCarrera->delete();

        return redirect()->route('grado-carreras.index')->with('success', 'Grado-Carrera eliminado exitosamente.');
    }
}
