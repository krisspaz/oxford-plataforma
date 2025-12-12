<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Matriculacion;
use App\Models\ContenidoMateria;
use App\Models\TareaEstudiante;
use App\Models\CMSUser;
use App\Models\Bimestre;

use Illuminate\Support\Facades\Storage;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class EstudianteTareaController extends Controller
{
    /**
     * Mostrar una lista de las tareas de los estudiantes.
     */

    public function materias()
    {
        // Obtener el usuario autenticado
        $user = CMSUser::find(CRUDBooster::myId());



        // Obtener la persona asociada al usuario
        $persona = $user->tareasAsignadas()->first();


        // Verificar si se encuentra la persona
        if ($persona) {
            // Obtener el estudiante relacionado con la persona
            $estudiante = Estudiante::with(['cgshges.materias.gestionMateria'])
                                    ->where('persona_id', $persona->id)
                                    ->firstOrFail();


            // dd( $estudiante->cgshges->materias->first()->gestionMateria->nombre);




            $inscripcion2 = Matriculacion::where('estudiante_id', $estudiante->id)
            ->orderBy('id', 'desc')
            ->with(['cgshges.materias.gestionMateria']) // Cargar materias y gestión
            ->first();

            $materiasPorCurso = $inscripcion2->cgshges->materias ?? collect();


            // Obtener materias agrupadas por curso

            // $materiasPorCurso = $estudiante->cgshges->materias;
            // dd($estudiante->cgshges->cursos->materias);
            //dd($inscripcion2);
            $estudiantes = $estudiante->id;
            //dd($materiasPorCurso);
            //dd( $materiasPorCurso2,$materiasPorCurso );


            // Retornar la vista con las materias agrupadas por curso
            return view('estudiantes.panelMaterias', compact('inscripcion2', 'materiasPorCurso', 'estudiantes'));
        }

    }

    public function index(Request $request)
    {
        $bimestreActual = Bimestre::actual()->first();
        $tareas = null;
        $añoActual = now()->year;

        if (CRUDBooster::myId()) {
            $user = CMSUser::find(CRUDBooster::myId());

            if ($user && in_array(strtolower($user->cmsPrivilege->name), ['administrativo', 'secretaria'])) {
                $tareasQuery = TareaEstudiante::with([
                    'tarea.bimestre',
                    'tarea.docente.persona',
                    'tarea.materia.gestionMateria',
                    'estudiante'
                ])
                ->whereHas('tarea', function ($q) use ($añoActual) {
                    $q->whereYear('created_at', $añoActual); // ✅ Solo tareas creadas este año
                });

                if ($request->filled('bimestre')) {
                    $tareasQuery->whereHas('tarea.bimestre', function ($q) use ($request) {
                        $q->where('nombre', $request->bimestre);
                    });
                }

                $tareas = $tareasQuery->get();
            } elseif ($user) {
                $tareasQuery = TareaEstudiante::with([
                    'tarea.bimestre',
                    'tarea.docente.persona',
                    'tarea.materia.gestionMateria',
                    'estudiante'
                ])
                ->whereHas('estudiante.persona', function ($q) {
                    $q->where('cms_users_id', CRUDBooster::myId());
                })
                ->whereHas('tarea', function ($q) use ($añoActual) {
                    $q->whereYear('created_at', $añoActual); // ✅ Solo tareas creadas este año
                });

                if ($request->filled('bimestre')) {
                    $tareasQuery->whereHas('tarea.bimestre', function ($q) use ($request) {
                        $q->where('nombre', $request->bimestre);
                    });
                }

                $tareas = $tareasQuery->get();
            }
        }

        return view('estudiantes.index_estudiante', compact('tareas'));

    }


    public function tareasPorMateria(Request $request, $materiaId)
    {
        $user = CMSUser::find(CRUDBooster::myId());
        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }

        $añoActual = now()->year;

        $tareasQuery = TareaEstudiante::with([
            'tarea.bimestre',
            'tarea.docente.persona',
            'tarea.materia.gestionMateria',
            'estudiante'
        ])
        ->whereHas('tarea.materia', function ($q) use ($materiaId) {
            $q->where('id', $materiaId);
        })
        ->whereHas('estudiante.persona', function ($q) use ($user) {
            $q->where('cms_users_id', $user->id);
        })
        ->whereHas('tarea', function ($q) use ($añoActual) {
            $q->whereYear('created_at', $añoActual);
        });

        // Aplicar filtro por bimestre si existe
        if ($request->filled('bimestre')) {
            $tareasQuery->whereHas('tarea.bimestre', function ($q) use ($request) {
                $q->where('nombre', $request->bimestre);
            });
        }

        $tareas = $tareasQuery->get();

        return view('estudiantes.tareas_por_materia', compact('tareas'));
    }



    /**
     * Mostrar el formulario para subir un archivo de tarea.
     */
    public function subirArchivo($id)
    {
        // Buscar la tarea
        $tarea = Tarea::findOrFail($id);

        // Buscar el estudiante y la relación con la tarea
        $tareaEstudiante = TareaEstudiante::where('tarea_id', $tarea->id)
                                          ->where('estudiante_id', CRUDBooster::myId())
                                          ->firstOrFail();

        // Verificar si el usuario tiene permisos para subir un archivo
        if (CRUDBooster::myId() != $tareaEstudiante->estudiante->persona->cmsUser->id) {
            abort(403, 'No tienes permiso para subir un archivo a esta tarea.');
        }

        return view('estudiantes.subir', compact('tarea'));
    }

    /**
     * Almacenar un archivo subido para una tarea.
     */
    public function guardarArchivo(Request $request, $id)
    {
        // Buscar la tarea
        $tarea = Tarea::findOrFail($id);

        // Buscar el estudiante y la relación con la tarea
        $tareaEstudiante = TareaEstudiante::where('tarea_id', $tarea->id)
                                          ->where('estudiante_id', CRUDBooster::myId())
                                          ->firstOrFail();

        // Verificar si el usuario tiene permisos para subir un archivo
        if (CRUDBooster::myId() != $tareaEstudiante->estudiante->persona->cmsUser->id) {
            abort(403, 'No tienes permiso para subir un archivo a esta tarea.');
        }

        // Validación del archivo
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240',  // Validación de PDF con un tamaño máximo de 10MB
        ]);

        // Almacenar el archivo
        $rutaArchivo = $request->file('archivo')->store('tareas_subidas', 'public');

        // Actualizar el registro en la tabla pivote TareaEstudiante
        $tareaEstudiante->update([
            'archivo' => $rutaArchivo,
            'fecha_entrega' => now(),
            'estado' => 'entregada',  // Estado de la tarea como 'entregada'
        ]);

        return back()->with('success', 'Tarea subida exitosamente.');
        // return redirect()->route('estudiantes.tareas.index')->with('success', 'Archivo subido correctamente.');
    }

    /**
     * Descargar un archivo de tarea.
     */
    public function descargarArchivo($id)
    {
        $tareaEstudiante = TareaEstudiante::findOrFail($id);

        // Verificar si el usuario tiene permisos para descargar el archivo
        if (CRUDBooster::myId() != $tareaEstudiante->estudiante->persona->cmsUser->id) {
            abort(403, 'No tienes permisos para descargar este archivo.');
        }

        // Ruta del archivo almacenado
        $path = storage_path('app/public/' . $tareaEstudiante->archivo);

        // Verificar si el archivo existe
        if (!file_exists($path)) {
            // return redirect()->route('estudiantes.tareas.index')->with('error', 'El archivo no existe.');
            return back()->with('error', 'El archivo no existe.');
        }

        // Descargar el archivo
        return response()->download($path);
    }

    public function uploadFile(Request $request, $tareaEstudianteId)
    {
        // Validar el archivo
        $request->validate([
            'archivo' => 'file',
        ]);

        // Buscar el registro de TareaEstudiante
        $tareaEstudiante = TareaEstudiante::findOrFail($tareaEstudianteId);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $path = $file->store('tareas_subidas', 'public');  // Almacenamos el archivo

            // Guardar la ruta del archivo en el modelo TareaEstudiante
            $tareaEstudiante->archivo = $path;
            $tareaEstudiante->fecha_entrega = now();
            $tareaEstudiante->estado = 'entregada';  // Cambiar el estado a "entregada"
            $tareaEstudiante->save();

            //   return redirect()->route('estudiantes.tareas.index')->with('success', 'Tarea subida exitosamente.');
            return back()->with('success', 'Tarea subida exitosamente.');
        }

        return back()->withErrors('Hubo un error al subir el archivo.');
    }

    public function downloadFile($tareaEstudianteId)
    {
        // Buscar el registro de la tarea del estudiante
        $tareaEstudiante = TareaEstudiante::findOrFail($tareaEstudianteId);

        // Verificar si existe el archivo
        if ($tareaEstudiante->archivo && Storage::exists('public/' . $tareaEstudiante->archivo)) {
            // Descargar el archivo desde el almacenamiento público
            return Storage::download('public/' . $tareaEstudiante->archivo);
        }

        return back()->withErrors('El archivo no está disponible.');
    }





    /**
 * Eliminar el archivo y la fecha de entrega de una tarea.
 */
    public function eliminarArchivo($tareaEstudianteId)
    {

        //dd($tareaEstudianteId);
        // Buscar el registro de TareaEstudiante
        $tareaEstudiante = TareaEstudiante::findOrFail($tareaEstudianteId);

        // Verificar si el archivo existe en el almacenamiento y eliminarlo
        if ($tareaEstudiante->archivo && Storage::exists('public/' . $tareaEstudiante->archivo)) {
            Storage::delete('public/' . $tareaEstudiante->archivo);  // Eliminar el archivo
        }

        // Actualizar el registro para eliminar la fecha de entrega y cambiar el estado
        $tareaEstudiante->update([
            'archivo' => null,        // Eliminar el archivo
            'fecha_entrega' => null,  // Eliminar la fecha de entrega
            'estado' => 'pendiente',  // Cambiar el estado de la tarea a pendiente
        ]);
        return back()->with('success', 'Archivo eliminado y tarea disponible para ser subida nuevamente.');

    }


    public function obtenerContenidoPorEstudiante($materia_id, Matriculacion $Inscripcion2)
    {

        // Obtener la materia y sus contenidos
        $materia = Materia::findOrFail($materia_id);
        $contenidos = ContenidoMateria::whereHas('materiaCursos', function ($query) use ($materia_id) {
            $query->where('materia_id', $materia_id);
        })
    ->whereYear('created_at', now()->year)
    ->get();



        $bimestres = Bimestre::whereHas('gestion', function ($query) use ($Inscripcion2) {
            $query->where('ciclo_escolar', $Inscripcion2->ciclo_escolar); // Accede al ciclo_escolar de 'Gestion' a través de Inscripcion2
        })->get();






        // Retornar la vista con los datos
        return view('contenido_estudiante.index', compact('materia', 'contenidos', 'bimestres'));
    }





}
