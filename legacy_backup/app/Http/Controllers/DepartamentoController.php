<?php



namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Estado;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with('estado')->get();
        return view('departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        $estados = Estado::all();
        return view('departamentos.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Departamento::create($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento creado exitosamente.');
    }

    public function show(Departamento $departamento)
    {
        return view('departamentos.show', compact('departamento'));
    }

    public function edit(Departamento $departamento)
    {
        $estados = Estado::all();
        return view('departamentos.edit', compact('departamento', 'estados'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $departamento->update($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento actualizado exitosamente.');
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();

        return redirect()->route('departamentos.index')->with('success', 'Departamento eliminado exitosamente.');
    }
}
