<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\Materia;
use App\Models\MateriaCurso;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\Bimestre;
use App\Models\CMSUser;
use App\Models\Curso;
use App\Models\Seccion;
use App\Models\Jornada;
use App\Models\Grado;
use App\Models\CmsNotification;
use CRUDBooster;

class DocenteTareaController extends Controller
{
    public function index(Request $request)
    {

        if ($request->has('limpiar')) {
            session()->forget(['tareas_filtro_buscar', 'tareas_filtro_bimestre']);
            return redirect($request->url());
        }

        // Guardar o recuperar valores del filtro
        if ($request->filled('buscar')) {
            session()->put('tareas_filtro_buscar', $request->buscar);
        } elseif (session()->has('tareas_filtro_buscar')) {
            $request->merge(['buscar' => session('tareas_filtro_buscar')]);
        }

        if ($request->filled('bimestre')) {
            session()->put('tareas_filtro_bimestre', $request->bimestre);
        } elseif (session()->has('tareas_filtro_bimestre')) {
            $request->merge(['bimestre' => session('tareas_filtro_bimestre')]);
        }

        $buscar = $request->input('buscar');
        $bimestreNombre = $request->input('bimestre');
        $userId = CRUDBooster::myId();


        $bimestres = Bimestre::whereHas('gestion.estado', function ($query) {
            $query->where('estado', 'Activo');
        })
    ->get();

        $añoActual = now()->year;
        $tareas = null;

        // Filtrado por docente
        if ($userId) {
            $tareas = Tarea::with(['materia.gestionMateria', 'docente.persona', 'estado', 'bimestre'])

                ->whereHas('docente.persona', function ($query) use ($userId) {
                    $query->where('cms_users_id', $userId);
                })
                ->whereHas('bimestre.gestion.estado', function ($query) {
                    $query->where('estado', 'Activo');
                });

            if (!empty($buscar)) {
                $tareas = $tareas->where(function ($query) use ($buscar) {
                    $query->where('titulo', 'like', "%{$buscar}%")
                          ->orWhereHas('materia.gestionMateria', function ($q) use ($buscar) {
                              $q->where('nombre', 'like', "%{$buscar}%");
                          });
                });
            }

            $tareas = $tareas->get(); // 👈 Solo aquí lo ejecutas
        }
        // Privilegios administrativos
        $user = CMSUser::find($userId);

        if ($user) {
            $tareas = Tarea::with(['materia.gestionMateria', 'docente.persona', 'estado', 'bimestre'])
                ->whereHas('bimestre.gestion.estado', function ($query) {
                    $query->where('estado', 'Activo');
                })
                ->when($buscar, function ($query) use ($buscar) {
                    $query->where(function ($q) use ($buscar) {
                        $q->where('titulo', 'like', "%{$buscar}%")
                          ->orWhereHas('materia.gestionMateria', function ($q2) use ($buscar) {
                              $q2->where('nombre', 'like', "%{$buscar}%");
                          });
                    });
                })
                ->get();
        }


        // Filtro por bimestre
        if ($bimestreNombre) {
            $bimestre = Bimestre::where('nombre', $bimestreNombre)->first();
            if ($bimestre) {
                $tareas = $tareas->filter(function ($tarea) use ($bimestre) {
                    return $tarea->bimestre_id == $bimestre->id;
                });
            }
        }

        return view('docentes.tareas.index', [
            'tareas' => $tareas,
            'buscar' => $buscar,
            'bimestre' => $bimestreNombre,
            'bimestres' => $bimestres
        ]);
    }



    public function create()
    {

        $estados = Estado::all();
        $bimestreActual = Bimestre::actual()->first();
        $bimestres = Bimestre::all();

        $userId = CRUDBooster::myId();

        $materias = collect();
        $docentes = collect();

        if ($userId) {
            $persona = Persona::where('cms_users_id', $userId)->first();

            if ($persona) {
                $docente = Docente::where('persona_id', $persona->id)->first();

                if ($docente) {
                    $materias = Materia::whereHas('materiasCursos', function ($query) use ($docente) {
                        $query->where('docente_id', $docente->id);
                    })
                      ->whereHas('estado', function ($query) {
                          $query->where('estado', 'Activo');
                      })
                      ->with(['gestionMateria', 'cgshe'])
                      ->get();

                    $docentes = collect([$docente->load('persona')]);
                }
            }
        }
        //dd($materias->first()->materiasCursos->first()->docente->first()->persona->nombres);




        return view('docentes.tareas.create', compact('buscar', 'bimestre', 'materias', 'docentes', 'estados', 'bimestreActual', 'bimestres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'bimestre_id' => 'required|exists:bimestres,id',
            'descripcion' => 'required|string',
            'punteo' => 'required|string',
            'materia_id' => 'required|array',
            'materia_id.*' => 'exists:materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'fexpiracion' => 'required|date',
            'tiempo_extra_automatico' => 'required|boolean',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        foreach ($request->materia_id as $materiaId) {
            Tarea::create([
                'titulo' => $request->titulo,
                'bimestre_id' => $request->bimestre_id,
                'descripcion' => $request->descripcion,
                'punteo' => $request->punteo,
                'materia_id' => $materiaId,
                'docente_id' => $request->docente_id,
                'fexpiracion' => $request->fexpiracion,
                'tiempo_extra_automatico' => $request->tiempo_extra_automatico,
                'estado_id' => $request->estado_id,
            ]);
        }

        return redirect()->route('docentes.tareas.index', $request->only(['buscar', 'bimestre']))
                     ->with('success', 'Tarea creada correctamente.');

        // return redirect()->route('docentes.tareas.index')->with('success', 'Tareas creadas exitosamente.');
    }

    public function show($id)
    {
        $tarea = Tarea::with(['materia', 'docente', 'estado'])->findOrFail($id);
        return view('docentes.tareas.show', compact('tarea'));
    }

    public function edit($id)
    {
        $tarea = Tarea::findOrFail($id);

        $estados = Estado::all();

        $estados = Estado::all();
        $bimestreActual = Bimestre::actual()->first();
        $bimestres = Bimestre::all();

        $userId = CRUDBooster::myId();

        $materias = collect();
        $docentes = collect();

        if ($userId) {
            $persona = Persona::where('cms_users_id', $userId)->first();

            if ($persona) {
                $docente = Docente::where('persona_id', $persona->id)->first();

                if ($docente) {
                    $materias = Materia::whereHas('materiasCursos', function ($query) use ($docente) {
                        $query->where('docente_id', $docente->id);
                    })
                    ->whereHas('estado', function ($query) {
                        $query->where('estado', 'Activo');
                    })
                    ->with(['gestionMateria', 'cgshe'])
                    ->get();

                    $docentes = collect([$docente->load('persona')]);
                }
            }
        }


        return view('docentes.tareas.edit', compact('tarea', 'materias', 'docentes', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'string',
             'punteo' => 'required|string',
            'materia_id' => 'exists:materias,id',
            'docente_id' => 'exists:docentes,id',
            'fexpiracion' => 'date',
            'tiempo_extra_automatico' => 'boolean',
            'estado_id' => 'exists:tb_estados,id',
        ]);

        try {
            $tarea = Tarea::findOrFail($id);
            $tarea->update($request->all());

            return redirect()->route('docentes.tareas.index', $request->only(['buscar', 'bimestre']))
                     ->with('success', 'Tarea actualizada correctamente 2.');

            //  return redirect()->route('docentes.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al actualizar la tarea. Por favor, inténtalo de nuevo.');
        }
    }

    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return redirect()->route('docentes.tareas.index', $request->only(['buscar', 'bimestre']))
                     ->with('success', 'Tarea eliminada correctamente.');

        // return redirect()->route('docentes.tareas.index')->with('success', 'Tarea eliminada exitosamente.');
    }

    public function asignar(Request $request, $id)
    {
        $request->validate([
            'estudiantes' => 'array',
            'estudiantes.*' => 'exists:estudiantes,id',
        ]);

        $tarea = Tarea::findOrFail($id);
        $estudiantesSeleccionados = $request->input('estudiantes', []);

        // Obtener los estudiantes que ya tienen esta tarea asignada
        $asignadosExistentes = $tarea->tareaEstudiantes()->pluck('estudiante_id')->toArray();

        // Determinar cuáles son nuevos (no están asignados aún)
        $nuevos = array_diff($estudiantesSeleccionados, $asignadosExistentes);

        foreach ($nuevos as $estudianteId) {
            $tarea->tareaEstudiantes()->create([
                'estudiante_id' => $estudianteId,
            ]);

            // --- ENVÍO DE NOTIFICACIÓN ---
            $estudiante = Estudiante::find($estudianteId);

            if ($estudiante && $estudiante->persona && $estudiante->persona->cmsUser->id) {
                $usuario_estudiante_id = $estudiante->persona->cmsUser->id;

                $mensaje = "Se te ha asignado la tarea '{$tarea->titulo}'";

                // URL directa al detalle de la tarea (puedes usar la misma vista de mostrar calificación)
                $urlNotificacion = route('estudiantes.tareas.porMateria', [
                      'materiaId' => $tarea->materia_id
                  ]);


                CmsNotification::create([
                    'content'      => $mensaje,
                     'id_cms_users' => $usuario_estudiante_id,
                    'url'          => $urlNotificacion,
                    'is_read'      => 0,
                ]);
            }
            // --- FIN NOTIFICACIÓN ---
        }

        // (Opcional) Eliminar los que ya no están en la selección
        $aEliminar = array_diff($asignadosExistentes, $estudiantesSeleccionados);

        if (!empty($aEliminar)) {
            $tarea->tareaEstudiantes()
                  ->whereIn('estudiante_id', $aEliminar)
                  ->delete();
        }

        return redirect()->route('docentes.tareas.index', $request->only(['buscar', 'bimestre']))
                         ->with('success', 'Tarea asignada exitosamente y notificaciones enviadas');
    }



    public function asignarForm($id)
    {
        // Obtener la tarea específica
        $tarea = Tarea::findOrFail($id);

        // Obtener la materia de la tarea
        $materia = $tarea->materia;


        // Obtener el docente relacionado con la tarea
        $docente = Docente::findOrFail($tarea->docente_id);

        // Obtener los cursos en los que el docente imparte esa materia
        $materiasCursos = MateriaCurso::where('docente_id', $docente->id)
                                      ->where('materia_id', $materia->id)
                                      ->get();



        // Filtrar los estudiantes que están en esos cursos usando cgshges_id
        $estudiantes = Estudiante::whereIn('cgshges_id', $materiasCursos->pluck('gshges_id'))
                                 ->get();
        $estudiantesAsignadosIds = $tarea->tareaEstudiantes()->pluck('estudiante_id')->toArray();



        return view('docentes.tareas.asignar', compact('tarea', 'estudiantes', 'estudiantesAsignadosIds'));

    }


    public function listado()
    {
        $grados = Grado::all();
        $cursos = Curso::all();
        $seccion = Seccion::all();
        $jornada = Jornada::all();

        // Obtener el usuario autenticado y su persona asociada
        $cmsUserId = CRUDBooster::myId();
        $persona = Persona::where('cms_users_id', $cmsUserId)->firstOrFail();

        // Obtener el docente asociado a la persona
        $docente = Docente::where('persona_id', $persona->id)->firstOrFail();

        // Obtener las materias que imparte el docente
        $materiasCursos = MateriaCurso::where('docente_id', $docente->id)->get();

        // Obtener las gestiones activas
        $gestionesActivas = Gestion::whereHas('estado', function ($query) {
            $query->where('estado', 'Activo'); // o usa estado_id = 1 si manejas ID
        })->pluck('ciclo_escolar');

        // Filtrar matriculaciones en gestiones activas
        $estudiantes = Estudiante::whereHas('inscripciones', function ($query) use ($gestionesActivas) {
            $query->whereIn('ciclo_escolar', $gestionesActivas);
        })
        ->whereIn('cgshges_id', $materiasCursos->pluck('gshges_id'))
        ->get();

        // Ordenar por apellidos y nombres
        $estudiantes = $estudiantes->sortBy(function ($estudiante) {
            return $estudiante->persona->apellidos . ' ' . $estudiante->persona->nombres;
        });

        // dd();

        return view('docentes.tareas.listado', compact('estudiantes', 'grados', 'cursos', 'seccion', 'jornada'));
    }





}
