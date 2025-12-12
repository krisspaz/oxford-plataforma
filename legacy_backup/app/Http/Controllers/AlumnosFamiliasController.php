<?php

namespace App\Http\Controllers;

use App\Models\PvAlumnosFamilias;
use App\Models\Alumno;
use App\Models\PvPadresTutores;
use Illuminate\Http\Request;

class AlumnosFamiliasController extends Controller
{

    public function index()
    {
        $alumnosFamilias = PvAlumnosFamilias::with(['alumno', 'padresTutores'])->get();
        return view('alumnos_familias.index', compact('alumnosFamilias'));
    }

    public function create()
    {
        $alumnos = Alumno::all();
        $padresTutores = PvPadresTutores::all();
        return view('alumnos_familias.create', compact('alumnos', 'padresTutores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:tb_alumnos,id',
            'padres_tutores_id' => 'required|exists:pv_padres_tutores,id',
        ]);

        PvAlumnosFamilias::create($request->all());

        return redirect()->route('alumnos_familias.index');
    }

    public function show(PvAlumnosFamilias $alumnosFamilia)
    {
        return view('alumnos_familias.show', compact('alumnosFamilia'));
    }

    public function edit(PvAlumnosFamilias $alumnosFamilia)
    {
        $alumnos = Alumno::all();
        $padresTutores = PvPadresTutores::all();
        return view('alumnos_familias.edit', compact('alumnosFamilia', 'alumnos', 'padresTutores'));
    }

    public function update(Request $request, PvAlumnosFamilias $alumnosFamilia)
    {
        $request->validate([
            'alumno_id' => 'required|exists:tb_alumnos,id',
            'padres_tutores_id' => 'required|exists:pv_padres_tutores,id',
        ]);

        $alumnosFamilia->update($request->all());

        return redirect()->route('alumnos_familias.index');
    }
}
