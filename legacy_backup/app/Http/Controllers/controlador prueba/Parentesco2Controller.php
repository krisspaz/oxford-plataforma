<?php
namespace App\Http\Controllers;

use App\Models\Parentesco;
use Illuminate\Http\Request;

class ParentescoController extends Controller
{
    public function index()
    {
        $parentescos = Parentesco::all();
        return view('parentescos.index', compact('parentescos'));
    }

    public function create()
    {
        return view('parentescos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'parentesco' => 'required|string|max:255',
        ]);

        Parentesco::create($request->all());
        return redirect()->route('parentescos.index')
                         ->with('success', 'Parentesco creado exitosamente.');
    }

    public function show(Parentesco $parentesco)
    {
        return view('parentescos.show', compact('parentesco'));
    }

    public function edit(Parentesco $parentesco)
    {
        return view('parentescos.edit', compact('parentesco'));
    }

    public function update(Request $request, Parentesco $parentesco)
    {
        $request->validate([
            'parentesco' => 'required|string|max:255',
        ]);

        $parentesco->update($request->all());
        return redirect()->route('parentescos.index')
                         ->with('success', 'Parentesco actualizado exitosamente.');
    }

    public function destroy(Parentesco $parentesco)
    {
        $parentesco->delete();
        return redirect()->route('parentescos.index')
                         ->with('success', 'Parentesco eliminado exitosamente.');
    }
}
