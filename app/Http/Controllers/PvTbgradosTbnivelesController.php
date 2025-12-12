<?php

namespace App\Http\Controllers;

use App\Models\PvTbgradosTbniveles;
use App\Models\Grado;
use App\Models\Nivel;
use App\Models\Estado;
use Illuminate\Http\Request;

class PvTbgradosTbnivelesController extends Controller
{
    public function index()
    {
        $relations = PvTbgradosTbniveles::with(['grado', 'nivel', 'estado'])->get();
        return view('pv_tbgrados_tbniveles.index', compact('relations'));
    }

    public function create()
    {
        $grados = Grado::all();
        $niveles = Nivel::all();
        $estados = Estado::all();
        return view('pv_tbgrados_tbniveles.create', compact('grados', 'niveles', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grado_id' => 'required|exists:tb_grados,id',
            'nivel_id' => 'required|exists:tb_niveles,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        PvTbgradosTbniveles::create($request->all());
        return redirect()->route('pv_tbgrados_tbniveles.index')->with('success', 'Relación creada exitosamente.');
    }

    public function show($id)
    {
        $relation = PvTbgradosTbniveles::with(['grado', 'nivel', 'estado'])->findOrFail($id);
        return view('pv_tbgrados_tbniveles.show', compact('relation'));
    }

    public function edit($id)
    {
        $relation = PvTbgradosTbniveles::findOrFail($id);
        $grados = Grado::all();
        $niveles = Nivel::all();
        $estados = Estado::all();
        return view('pv_tbgrados_tbniveles.edit', compact('relation', 'grados', 'niveles', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grado_id' => 'required|exists:tb_grados,id',
            'nivel_id' => 'required|exists:tb_niveles,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $relation = PvTbgradosTbniveles::findOrFail($id);
        $relation->update($request->all());
        return redirect()->route('pv_tbgrados_tbniveles.index')->with('success', 'Relación actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $relation = PvTbgradosTbniveles::findOrFail($id);
        $relation->delete();
        return redirect()->route('pv_tbgrados_tbniveles.index')->with('success', 'Relación eliminada exitosamente.');
    }
}
