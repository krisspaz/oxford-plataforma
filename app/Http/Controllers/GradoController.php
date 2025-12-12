<?php

namespace App\Http\Controllers;

use App\Models\Grado;

use App\Models\Estado;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function index()
    {
        $grados = Grado::with([ 'estado'])->get();
        return view('grados.index', compact('grados'));
    }

    public function create()
    {
    
        $estados = Estado::all();
        return view('grados.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Grado::create($request->all());

        return redirect()->route('grados.index')->with('success', 'Grado creado exitosamente.');
    }

    public function show(Grado $grado)
    {
        return view('grados.show', compact('grado'));
    }

    public function edit(Grado $grado)
    {
     
        $estados = Estado::all();
        return view('grados.edit', compact('grado', 'estados'));
    }

    public function update(Request $request, Grado $grado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
          
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $grado->update($request->all());

        return redirect()->route('grados.index')->with('success', 'Grado actualizado exitosamente.');
    }

    public function destroy(Grado $grado)
    {
        $grado->delete();

        return redirect()->route('grados.index')->with('success', 'Grado eliminado exitosamente.');
    }
}
