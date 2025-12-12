<?php

namespace App\Http\Controllers;

use App\Models\PaqueteDetalle;
use App\Models\Paquete;
use Illuminate\Http\Request;

class PaqueteDetalleController extends Controller
{
    public function index()
    {
        $detalles = PaqueteDetalle::with('paquete')->get();
        return view('paquete_detalles.index', compact('detalles'));
    }

    public function create()
    {
        $paquetes = Paquete::all();
        return view('paquete_detalles.create', compact('paquetes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paquete_id' => 'required|exists:paquetes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' ,
            'precio' => 'required|numeric|min:0',
             'tipo_comprobante' =>   'required',
             'tipo_producto' =>   'required',
        ]);

        PaqueteDetalle::create($request->all());

        return redirect()->route('paquete_detalles.index')->with('success', 'Registros Almacenados Exitosamente.');
    }

    public function show(PaqueteDetalle $paqueteDetalle)
    {
        return view('paquete_detalles.show', compact('paqueteDetalle'));
    }

    public function edit(PaqueteDetalle $paqueteDetalle)
    {
        $paquetes = Paquete::all();
        return view('paquete_detalles.edit', compact('paqueteDetalle', 'paquetes'));
    }

    public function update(Request $request, PaqueteDetalle $paqueteDetalle)
    {
        $request->validate([
            'paquete_id' => 'required|exists:paquetes,id',
            'nombre' => 'required|string|max:255',
            'descripcion',
            'precio' => 'required|numeric|min:0',
            'tipo_comprobante' =>   'required',
            'tipo_producto' =>   'required',
        ]);

        $paqueteDetalle->update($request->all());

        return redirect()->route('paquete_detalles.index')->with('success', 'Registros actualizados exitosamente.');
    }

    public function destroy(PaqueteDetalle $paqueteDetalle)
    {
        $paqueteDetalle->delete();

        return redirect()->route('paquete_detalles.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
