<?php

namespace App\Http\Controllers;

use App\Models\NivelSucursal;
use App\Models\Sucursal;
use App\Models\Nivel;
use App\Models\Estado;
use Illuminate\Http\Request;

class NivelSucursalController extends Controller
{
    public function index()
    {
        $niveles_sucursals = NivelSucursal::all();
        return view('niveles_sucursals.index', compact('niveles_sucursals'));
    }

    public function create()
    {
        $sucursals = Sucursal::all();
        $niveles = Nivel::all();
        $estados = Estado::all();
        return view('niveles_sucursals.create', compact('sucursals', 'niveles', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:tb_sucursals,id',
            'nivel_id' => 'required|exists:tb_niveles,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        NivelSucursal::create($request->all());

        return redirect()->route('niveles_sucursals.index')->with('success', 'Nivel-Sucursal creado correctamente.');
    }

    public function show(NivelSucursal $niveles_sucursal)
    {
        return view('niveles_sucursals.show', compact('niveles_sucursal'));
    }

    public function edit(NivelSucursal $niveles_sucursal)
    {
        $sucursals = Sucursal::all();
        $niveles = Nivel::all();
        $estados = Estado::all();
        return view('niveles_sucursals.edit', compact('niveles_sucursal', 'sucursals', 'niveles', 'estados'));
    }

    public function update(Request $request, NivelSucursal $niveles_sucursal)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:tb_sucursals,id',
            'nivel_id' => 'required|exists:tb_niveles,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $niveles_sucursal->update($request->all());

        return redirect()->route('niveles_sucursals.index')->with('success', 'Nivel-Sucursal actualizado correctamente.');
    }

    public function destroy(NivelSucursal $niveles_sucursal)
    {
        $niveles_sucursal->delete();
        return redirect()->route('niveles_sucursals.index')->with('success', 'Nivel-Sucursal eliminado correctamente.');
    }
}
