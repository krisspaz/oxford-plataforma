<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Telefono;
use App\Models\SucursalTelefono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SucursalTelefonoController extends Controller
{
    public function index()
    {
        $sucursalTelefonos = SucursalTelefono::with(['sucursal', 'telefono'])->paginate(10);
        return view('sucursal_telefonos.index', compact('sucursalTelefonos'));
    }

    public function create()
    {
        $sucursals = Sucursal::all();
        $telefonos = Telefono::all();
        return view('sucursal_telefonos.create', compact('sucursals', 'telefonos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_id' => 'required|exists:tb_sucursals,id',
            'telefono_id' => 'required|exists:tb_telefonos,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        SucursalTelefono::create($request->all());
        return redirect()->route('sucursal_telefonos.index')->with('success', 'Relación sucursal-telefono creada exitosamente.');
    }

    public function edit($id)
    {
        $sucursalTelefono = SucursalTelefono::findOrFail($id);
        $sucursals = Sucursal::all();
        $telefonos = Telefono::all();
        return view('sucursal_telefonos.edit', compact('sucursalTelefono', 'sucursals', 'telefonos'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_id' => 'required|exists:tb_sucursals,id',
            'telefono_id' => 'required|exists:tb_telefonos,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sucursalTelefono = SucursalTelefono::findOrFail($id);
        $sucursalTelefono->update($request->all());
        return redirect()->route('sucursal_telefonos.index')->with('success', 'Relación sucursal-telefono actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $sucursalTelefono = SucursalTelefono::findOrFail($id);
        $sucursalTelefono->delete();
        return redirect()->route('sucursal_telefonos.index')->with('success', 'Relación sucursal-telefono eliminada exitosamente.');
    }

    public function show($id)
    {
        $sucursalTelefono = SucursalTelefono::with(['sucursal', 'telefono'])->findOrFail($id);
        return view('sucursal_telefonos.show', compact('sucursalTelefono'));
    }
}
