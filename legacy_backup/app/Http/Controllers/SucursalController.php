<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        // Obtén las sucursales con paginación
        $sucursals = Sucursal::with(['municipio', 'estado'])->paginate(10);
        return view('sucursals.index', compact('sucursals'));
    }

    public function create()
    {
        $departamentos = Departamento::all();
        $estados = Estado::all();
       
        return view('sucursals.create', compact('departamentos', 'estados'));
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre_sucursal' => 'required|string|max:255',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
         
        ]);

        $sucursal = Sucursal::create([
            'nombre_sucursal' => $request->nombre_sucursal,
            'municipio_id' => $request->municipio_id,
            'direccion' => $request->direccion,
            'estado_id' => $request->estado_id,
        ]);

      

        return redirect()->route('sucursals.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Sucursal $sucursal)
    {
        $sucursal->load(['municipio', 'estado']);
        return view('sucursals.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal)
    {
        $departamentos = Departamento::all();
        $estados = Estado::all();
       
        $municipios = Municipio::where('departamento_id', $sucursal->municipio->departamento_id)->get();
        return view('sucursals.edit', compact('sucursal', 'departamentos', 'estados', 'municipios'));
    }

   

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();
        return redirect()->route('sucursals.index')->with('success', 'Sucursal eliminada exitosamente.');
    }

    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)->pluck('municipio', 'id');
        return response()->json($municipios);
    }


    public function update(Request $request, Sucursal $sucursal)
    {
        // Validar los datos
        $request->validate([
            'nombre_sucursal' => 'required|string|max:255',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
       
        ]);

        // Actualizar la sucursal
        $sucursal->update([
            'nombre_sucursal' => $request->nombre_sucursal,
            'municipio_id' => $request->municipio_id,
            'direccion' => $request->direccion,
            'estado_id' => $request->estado_id,
        ]);

        // Asociar teléfonos con la sucursal
        $sucursal->telefonos()->sync($request->telefonos);

        return redirect()->route('sucursals.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

}
