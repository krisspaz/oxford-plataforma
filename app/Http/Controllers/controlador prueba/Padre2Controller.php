<?php

namespace App\Http\Controllers;

use App\Models\Padre;
use App\Models\Alumno;
use App\Models\Parentesco;
use Illuminate\Http\Request;

class PadreController extends Controller
{
    public function index()
    {
        $padres = Padre::with('parentesco')->get();
        return view('padres.index', compact('padres'));
    }

    public function create()
    {
        $parentescos = Parentesco::all();
        return view('padres.create', compact('parentescos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'padre.nombre' => 'nullable|string|max:255',
            'padre.apellido' => 'nullable|string|max:255',
            'padre.email' => 'nullable|email|unique:padres,email',
            'padre.telefono' => 'nullable|string|max:15',
            'padre.direccion' => 'nullable|string|max:255',
            'padre.parentesco_id' => 'nullable|exists:parentescos,id',
            
            'madre.nombre' => 'nullable|string|max:255',
            'madre.apellido' => 'nullable|string|max:255',
            'madre.email' => 'nullable|email|unique:padres,email',
            'madre.telefono' => 'nullable|string|max:15',
            'madre.direccion' => 'nullable|string|max:255',
            'madre.parentesco_id' => 'nullable|exists:parentescos,id',
            
            'encargado.nombre' => 'nullable|string|max:255',
            'encargado.apellido' => 'nullable|string|max:255',
            'encargado.email' => 'nullable|email|unique:padres,email',
            'encargado.telefono' => 'nullable|string|max:15',
            'encargado.direccion' => 'nullable|string|max:255',
            'encargado.parentesco_id' => 'nullable|exists:parentescos,id',

            'alumnos.*.codigo' => 'required|string|max:255',
            'alumnos.*.nombre' => 'required|string|max:255',
            'alumnos.*.apellido' => 'required|string|max:255',
            'alumnos.*.fecha_nacimiento' => 'required|date',
            'alumnos.*.grado' => 'required|string|max:255',
        ]);

        $padresData = [
            'padre' => 'Padre',
            'madre' => 'Madre',
            'encargado' => 'Encargado'
        ];

        foreach ($padresData as $key => $tipo) {
            if ($request->has("$key.nombre")) {
                Padre::updateOrCreate(
                    ['email' => $request->input("$key.email")],
                    array_merge($request->input($key), ['parentesco_id' => Parentesco::where('parentesco', $tipo)->first()->id])
                );
            }
        }

        foreach ($request->input('alumnos') as $alumnoData) {
            Alumno::updateOrCreate(
                ['codigo' => $alumnoData['codigo']],
                [
                    'nombre' => $alumnoData['nombre'],
                    'apellido' => $alumnoData['apellido'],
                    'fecha_nacimiento' => $alumnoData['fecha_nacimiento'],
                    'grado' => $alumnoData['grado'],
                    'padre_id' => Padre::where('parentesco_id', Parentesco::where('parentesco', 'Padre')->first()->id)
                                      ->where('email', $request->input('padre.email'))->first()->id ?? null,
                    'madre_id' => Padre::where('parentesco_id', Parentesco::where('parentesco', 'Madre')->first()->id)
                                      ->where('email', $request->input('madre.email'))->first()->id ?? null,
                    'encargado_id' => Padre::where('parentesco_id', Parentesco::where('parentesco', 'Encargado')->first()->id)
                                          ->where('email', $request->input('encargado.email'))->first()->id ?? null,
                ]
            );
        }

        return redirect()->route('alumnos.index')->with('success', 'Datos creados exitosamente.');
    }
}
