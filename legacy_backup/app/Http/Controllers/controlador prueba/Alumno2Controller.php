<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Padre;
use App\Models\Parentesco;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::with('padre', 'madre', 'encargado')->get();
        return view('alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view('alumnos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'padre_nombre' => 'nullable|string|max:255',
            'padre_apellido' => 'nullable|string|max:255',
            'padre_email' => 'nullable|email|max:255',
            'padre_telefono' => 'nullable|string|max:20',
            'padre_direccion' => 'nullable|string|max:255',
            'madre_nombre' => 'nullable|string|max:255',
            'madre_apellido' => 'nullable|string|max:255',
            'madre_email' => 'nullable|email|max:255',
            'madre_telefono' => 'nullable|string|max:20',
            'madre_direccion' => 'nullable|string|max:255',
            'encargado_nombre' => 'nullable|string|max:255',
            'encargado_apellido' => 'nullable|string|max:255',
            'encargado_email' => 'nullable|email|max:255',
            'encargado_telefono' => 'nullable|string|max:20',
            'encargado_direccion' => 'nullable|string|max:255',
            'alumno_nombre' => 'required|string|max:255',
            'alumno_apellido' => 'required|string|max:255',
            'alumno_fecha_nacimiento' => 'required|date',
            'alumno_grado' => 'required|string|max:255',
        ]);

        // Crear el padre si no existe
        $padreId = null;
        if ($request->filled('padre_nombre') && $request->filled('padre_apellido')) {
            $padreParentesco = Parentesco::firstOrCreate(['parentesco' => 'Padre']);
            $padre = Padre::create([
                'nombre' => $request->input('padre_nombre'),
                'apellido' => $request->input('padre_apellido'),
                'email' => $request->input('padre_email'),
                'telefono' => $request->input('padre_telefono'),
                'direccion' => $request->input('padre_direccion'),
                'parentesco_id' => $padreParentesco->id,
            ]);
            $padreId = $padre->id;
        }

        // Crear la madre si no existe
        $madreId = null;
        if ($request->filled('madre_nombre') && $request->filled('madre_apellido')) {
            $madreParentesco = Parentesco::firstOrCreate(['parentesco' => 'Madre']);
            $madre = Padre::create([
                'nombre' => $request->input('madre_nombre'),
                'apellido' => $request->input('madre_apellido'),
                'email' => $request->input('madre_email'),
                'telefono' => $request->input('madre_telefono'),
                'direccion' => $request->input('madre_direccion'),
                'parentesco_id' => $madreParentesco->id,
            ]);
            $madreId = $madre->id;
        }

        // Crear el encargado si no existe
        $encargadoId = null;
        if ($request->filled('encargado_nombre') && $request->filled('encargado_apellido')) {
            $encargadoParentesco = Parentesco::firstOrCreate(['parentesco' => 'Encargado']);
            $encargado = Padre::create([
                'nombre' => $request->input('encargado_nombre'),
                'apellido' => $request->input('encargado_apellido'),
                'email' => $request->input('encargado_email'),
                'telefono' => $request->input('encargado_telefono'),
                'direccion' => $request->input('encargado_direccion'),
                'parentesco_id' => $encargadoParentesco->id,
            ]);
            $encargadoId = $encargado->id;
        }

        // Crear el alumno con las relaciones correspondientes
        Alumno::create([
            'codigo' => $request->input('alumno_codigo'),
            'nombre' => $request->input('alumno_nombre'),
            'apellido' => $request->input('alumno_apellido'),
            'fecha_nacimiento' => $request->input('alumno_fecha_nacimiento'),
            'grado' => $request->input('alumno_grado'),
            'padre_id' => $padreId,
            'madre_id' => $madreId,
            'encargado_id' => $encargadoId,
        ]);

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno y sus datos relacionados creados exitosamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('padre', 'madre', 'encargado');
        return view('alumnos.show', compact('alumno'));
    }

    public function edit($id)
    {
        try {
            $alumno = Alumno::with(['padre', 'madre', 'encargado'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado.');
        }

        $parentescos = Parentesco::all(); // Para mostrar las opciones en el formulario
        return view('alumnos.edit', compact('alumno', 'parentescos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'padre_nombre' => 'nullable|string|max:255',
            'padre_apellido' => 'nullable|string|max:255',
            'padre_email' => 'nullable|email|max:255',
            'padre_telefono' => 'nullable|string|max:20',
            'padre_direccion' => 'nullable|string|max:255',
            'madre_nombre' => 'nullable|string|max:255',
            'madre_apellido' => 'nullable|string|max:255',
            'madre_email' => 'nullable|email|max:255',
            'madre_telefono' => 'nullable|string|max:20',
            'madre_direccion' => 'nullable|string|max:255',
            'encargado_nombre' => 'nullable|string|max:255',
            'encargado_apellido' => 'nullable|string|max:255',
            'encargado_email' => 'nullable|email|max:255',
            'encargado_telefono' => 'nullable|string|max:20',
            'encargado_direccion' => 'nullable|string|max:255',
            'alumno_codigo' => 'required|string|max:255',
            'alumno_nombre' => 'required|string|max:255',
            'alumno_apellido' => 'required|string|max:255',
            'alumno_fecha_nacimiento' => 'required|date',
            'alumno_grado' => 'required|string|max:255',
        ]);
    
        $alumno = Alumno::findOrFail($id);
    
        // Actualizar o crear el padre
        if ($request->filled('padre_nombre') && $request->filled('padre_apellido')) {
            $padreParentesco = Parentesco::firstOrCreate(['parentesco' => 'Padre']);
            $padre = Padre::updateOrCreate(
                ['id' => $alumno->padre_id],
                [
                    'nombre' => $request->input('padre_nombre'),
                    'apellido' => $request->input('padre_apellido'),
                    'email' => $request->input('padre_email'),
                    'telefono' => $request->input('padre_telefono'),
                    'direccion' => $request->input('padre_direccion'),
                    'parentesco_id' => $padreParentesco->id,
                ]
            );
            $alumno->padre_id = $padre->id;
        }
    
        // Actualizar o crear la madre
        if ($request->filled('madre_nombre') && $request->filled('madre_apellido')) {
            $madreParentesco = Parentesco::firstOrCreate(['parentesco' => 'Madre']);
            $madre = Padre::updateOrCreate(
                ['id' => $alumno->madre_id],
                [
                    'nombre' => $request->input('madre_nombre'),
                    'apellido' => $request->input('madre_apellido'),
                    'email' => $request->input('madre_email'),
                    'telefono' => $request->input('madre_telefono'),
                    'direccion' => $request->input('madre_direccion'),
                    'parentesco_id' => $madreParentesco->id,
                ]
            );
            $alumno->madre_id = $madre->id;
        }
    
        // Actualizar o crear el encargado
        if ($request->filled('encargado_nombre') && $request->filled('encargado_apellido')) {
            $encargadoParentesco = Parentesco::firstOrCreate(['parentesco' => 'Encargado']);
            $encargado = Padre::updateOrCreate(
                ['id' => $alumno->encargado_id],
                [
                    'nombre' => $request->input('encargado_nombre'),
                    'apellido' => $request->input('encargado_apellido'),
                    'email' => $request->input('encargado_email'),
                    'telefono' => $request->input('encargado_telefono'),
                    'direccion' => $request->input('encargado_direccion'),
                    'parentesco_id' => $encargadoParentesco->id,
                ]
            );
            $alumno->encargado_id = $encargado->id;
        }
    
        // Actualizar el alumno
        $alumno->update([
            'codigo' => $request->input('alumno_codigo'),
            'nombre' => $request->input('alumno_nombre'),
            'apellido' => $request->input('alumno_apellido'),
            'fecha_nacimiento' => $request->input('alumno_fecha_nacimiento'),
            'grado' => $request->input('alumno_grado'),
        ]);
    
        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno actualizado exitosamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno eliminado exitosamente.');
    }
}
