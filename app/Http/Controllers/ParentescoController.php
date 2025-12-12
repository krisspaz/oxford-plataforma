<?php

namespace App\Http\Controllers;

use App\Models\Parentesco;
use App\Models\Estado;
use Illuminate\Http\Request;

class ParentescoController extends Controller
{
    public function index()
    {
        $parentescos = Parentesco::with('estado')->paginate(10);
        return view('parentescos.index', compact('parentescos'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('parentescos.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parentesco' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Parentesco::create($request->all());
        return redirect()->route('parentescos.index')->with('success', 'Parentesco creado con éxito.');
    }

    public function show(Parentesco $parentesco)
    {
        return view('parentescos.show', compact('parentesco'));
    }

    public function edit(Parentesco $parentesco)
    {
        $estados = Estado::all();
        return view('parentescos.edit', compact('parentesco', 'estados'));
    }

    public function update(Request $request, Parentesco $parentesco)
    {
        $request->validate([
            'parentesco' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $parentesco->update($request->all());
        return redirect()->route('parentescos.index')->with('success', 'Parentesco actualizado con éxito.');
    }

    public function destroy(Parentesco $parentesco)
    {
        $parentesco->delete();
        return redirect()->route('parentescos.index')->with('success', 'Parentesco eliminado con éxito.');
    }
}
