<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\TareaEstudiante;
use App\Models\CalificacionTarea;
use App\Models\Bimestre;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    // Mostrar todas las tareas creadas por el docente
    public function index()
    {
        $tareas = Tarea::with('estudiantes')->get();




        return view('tareas.index', compact('tareas'));
    }

    // Formulario para crear una nueva tarea
    public function create()
    {
        $materias = Materia::all();
        $docentes = Docente::all();
        $estados = Estado::all();
        // Obtener el bimestre actual
      
         
        return view('tareas.create', compact('materias', 'docentes', 'estados'));
    }

    // Guardar una nueva tarea en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'materia_id' => 'required|exists:tb_materias,id',
            'docente_id' => 'required|exists:tb_docentes,id',
            'fexpiracion' => 'required|date',
            'tiempo_extra_automatico' => 'required|boolean',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        Tarea::create($request->all());

        return redirect()->route('tareas.index')->with('success', 'Tarea creada exitosamente.');
    }

    // Mostrar detalles de una tarea y los estudiantes asignados
    public function show($id)
    {
        $tarea = Tarea::with(['materia', 'docente', 'estado'])->findOrFail($id);
        return view('docentes.tareas.show', compact('tarea'));
    }

    // Formulario para asignar estudiantes a una tarea
    public function asignar($id)
    {
        $tarea = Tarea::findOrFail($id);
        //$estudiantes = Estudiante::all(); // Obtener todos los estudiantes
        return view('tareas.asignar', compact('tarea', 'estudiantes'));
    }

    // Guardar asignación de estudiantes
    public function guardarAsignacion(Request $request, $id)
    {
        $request->validate([
            'estudiantes' => 'required|array',
        ]);

        foreach ($request->estudiantes as $estudiante_id) {
            TareaEstudiante::create([
                'tarea_id' => $id,
                'estudiante_id' => $estudiante_id,
            ]);
        }

        return redirect()->route('tareas.show', $id)->with('success', 'Estudiantes asignados exitosamente.');
    }

    // Subir archivo por parte del estudiante
    public function subirTarea(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,docx,zip',
        ]);

        $tareaEstudiante = TareaEstudiante::findOrFail($id);
        $rutaArchivo = $request->file('archivo')->store('tareas');

        $tareaEstudiante->archivo_subido = $rutaArchivo;
        $tareaEstudiante->fecha_subida = now();
        $tareaEstudiante->save();

        return redirect()->back()->with('success', 'Tarea subida exitosamente.');
    }

    // Calificar una tarea
    public function calificar(Request $request, $id)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
            'comentarios' => 'nullable|string',
        ]);

        $tareaEstudiante = TareaEstudiante::findOrFail($id);

        CalificacionTarea::create([
            'tarea_estudiante_id' => $tareaEstudiante->id,
            'calificacion' => $request->calificacion,
            'comentarios' => $request->comentarios,
        ]);

        return redirect()->back()->with('success', 'Tarea calificada exitosamente.');
    }
}
