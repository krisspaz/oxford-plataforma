<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Estado;
use App\Models\Departamento;

use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alumnos = Alumno::all();
        return view('alumnos.index', compact('alumnos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departamentos = Departamento::all();
        $estados = Estado::all();
        return view('alumnos.create', compact('departamentos', 'estados'));
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
            'codigo' => 'required',
            'carne' => 'required',
            'nombre' => 'required',
            'apellidos' => 'required',
            'genero' => 'required|in:M,F',
            'cui' => 'required|unique:tb_alumnos',
            'fecha_nacimiento' => 'required|date',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'required',
            'telefono' => 'nullable',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Alumno::create($request->all());

        return redirect()->route('alumnos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Alumno $alumno)
    {
        return view('alumnos.show', compact('alumno'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Alumno $alumno)
    {
        $departamentos = Departamento::all();
        $estados = Estado::all();
        return view('alumnos.edit', compact('alumno', 'departamentos', 'estados'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'codigo' => 'required|unique:tb_alumnos,codigo,' . $alumno->id,
            'carne' => 'required|unique:tb_alumnos,carne,' . $alumno->id,
            'nombre' => 'required',
            'apellidos' => 'required',
            'genero' => 'required|in:M,F',
            'cui' => 'required|unique:tb_alumnos,cui,' . $alumno->id,
            'fecha_nacimiento' => 'required|date',
            'municipio_id' => 'required|exists:tb_municipios,id',
            'direccion' => 'required',
            'telefono' => 'nullable',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $alumno->update($request->all());

        return redirect()->route('alumnos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index');
    }
}
