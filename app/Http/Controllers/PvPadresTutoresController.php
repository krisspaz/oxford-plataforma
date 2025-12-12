<?php

namespace App\Http\Controllers;

use App\Models\PvPadresTutores;
use App\Models\Padre;
use App\Models\Madre;
use App\Models\Encargado;
use Illuminate\Http\Request;

class PvPadresTutoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $padres_tutores = PvPadresTutores::all();
        return view('pv_padres_tutores.index', compact('padres_tutores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $padres = Padre::all();
        $madres = Madre::all();
        $encargados = Encargado::all();
        return view('pv_padres_tutores.create', compact('padres', 'madres', 'encargados'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigofamiliar' => 'nullable',
            'padre_id' => 'nullable|exists:tb_padres,id',
            'madre_id' => 'nullable|exists:tb_madres,id',
            'encargado_id' => 'nullable|exists:tb_encargados,id',
        ]);

        PvPadresTutores::create($request->all());

        return redirect()->route('pv_padres_tutores.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $padres_tutor = PvPadresTutores::findOrFail($id);
        return view('pv_padres_tutores.show', compact('padres_tutor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $padres_tutor = PvPadresTutores::findOrFail($id);
        $padres = Padre::all();
        $madres = Madre::all();
        $encargados = Encargado::all();
        return view('pv_padres_tutores.edit', compact('padres_tutor', 'padres', 'madres', 'encargados'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'codigofamiliar' => 'unique:pv_padres_tutores,codigofamiliar,' . $id,
            'padre_id' => 'nullable|exists:tb_padres,id',
            'madre_id' => 'nullable|exists:tb_madres,id',
            'encargado_id' => 'nullable|exists:tb_encargados,id',
        ]);

        $padres_tutor = PvPadresTutores::findOrFail($id);
        $padres_tutor->update($request->all());

        return redirect()->route('pv_padres_tutores.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $padres_tutor = PvPadresTutores::findOrFail($id);
        $padres_tutor->delete();

        return redirect()->route('pv_padres_tutores.index')->with('success', 'Registro eliminado correctamente.');
    }
}
