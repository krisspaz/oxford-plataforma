<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuadroNota;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Bimestre;
use App\Models\CMSUser;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Matriculacion;
use App\Models\MateriaCurso;
use App\Models\Familia;
use App\Models\Persona;
use App\Models\Cuota;
use App\Models\Grado;
use App\Models\Estado;
// <-- Importamos el controlador


use App\Models\CalificacionTarea;
use Illuminate\Support\Facades\DB;

use crocodicstudio\crudbooster\helpers\CRUDBooster;

class CuadroNotaController extends Controller
{
    public function index(Request $request)
    {
        $notas = collect(); // Colección vacía inicial
        $userId = CRUDBooster::myId();
        $añoActual = now()->year;

        // Filtros desde request
        $gradoId = $request->input('grado_id');
        $materiaId = $request->input('materia_id');
        $buscar = $request->input('buscar');

        // Número de registros por página (default 10)
        $perPage = $request->input('per_page', 10);

        // Para llenar los combos
        $grados = Grado::all();
        $materias = Materia::all();

        if ($userId) {
            $user = CMSUser::find($userId);

            // Ciclos escolares activos
            $ciclosActivos = Gestion::whereHas('estado', function ($query) {
                $query->where('estado', 'Activo');
            })
                ->pluck('ciclo_escolar');

            $query = CuadroNota::with([
                    'estudiante.cgshges.grados',
                    'materia.gestionMateria',
                    'estudiante.persona',
                    'bimestres'
                ])
                ->whereIn('ciclo_escolar', $ciclosActivos);

            // Filtrar por docente
            if ($user) {
                $persona = Persona::where('cms_users_id', $userId)->first();
                $docenteId = $persona ? Docente::where('persona_id', $persona->id)->value('id') : null;

                if ($docenteId) {
                    $query->whereHas('materia.materiasCursos', function ($q) use ($docenteId) {
                        $q->where('docente_id', $docenteId);
                    });
                }
            }

            // Filtro por grado
            if (!empty($gradoId)) {
                $query->whereHas('estudiante.cgshges.grados', function ($q) use ($gradoId) {
                    $q->where('id', $gradoId);
                });
            }

            // Filtro por materia
            if (!empty($materiaId)) {
                $query->where('materia_id', $materiaId);
            }

            // Filtro por búsqueda
            if (!empty($buscar)) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhereHas('materia.gestionMateria', function ($q2) use ($buscar) {
                          $q2->where('nombre', 'like', "%{$buscar}%");
                      });
                });
            }

            /** -----------------------------------------
             *  PAGINACIÓN DINÁMICA (CON SOPORTE PARA "ALL")
             *  ----------------------------------------- */
            if ($perPage === 'all') {
                // Mostrar todos los resultados SIN paginación
                $notas = $query->get();
            } else {
                // Convertir a número seguro
                $perPage = is_numeric($perPage) ? intval($perPage) : 10;

                $notas = $query->paginate($perPage)->appends($request->query());
            }
        }

        return view('docentes.notas.index', compact(
            'notas',
            'grados',
            'materias',
            'gradoId',
            'materiaId',
            'buscar',
            'perPage'
        ));
    }




    public function create()
    {
        $bimestres = Bimestre::all();
        $bimestreActual = Bimestre::actual()->first();
        $userId = CRUDBooster::myId();

        $materias = collect();
        $estudiantes = collect();

        if ($userId) {
            $materias = Materia::whereHas('materiasCursos.docente.persona', function ($query) use ($userId) {
                $query->where('cms_users_id', $userId);
            })->get();

            $materiasCursos = MateriaCurso::whereHas('docente.persona', function ($query) use ($userId) {
                $query->where('cms_users_id', $userId);
            })->get();

            $estudiantes = Estudiante::whereIn('cgshges_id', $materiasCursos->pluck('gshges_id'))->get();
        }

        // dd($estudiantes->first()->tareaEstudiantes->first()->tarea->bimestre->nombre);
        // dd($estudiantes->first()->tareaEstudiantes->first()->calificacion);


        return view('docentes.notas.create', compact('materias', 'estudiantes', 'bimestres', 'bimestreActual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'bimestre' => 'required|string|max:50',
            'ciclo_escolar' => 'required|string|max:20',
            'notas' => 'required|array',
            'notas.*.estudiante_id' => 'required|exists:estudiantes,id',
            'notas.*.nota_final' => 'nullable|numeric|min:0|max:100',
            'notas.*.nota_acumulada' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->notas as $nota) {
            CuadroNota::create([
                'estudiante_id' => $nota['estudiante_id'],
                'materia_id' => $request->materia_id,
                'bimestre' => $request->bimestre,
                'nota_final' => $nota['nota_final'],
                'nota_acumulada' => $nota['nota_acumulada'],
                'ciclo_escolar' => $request->ciclo_escolar,
            ]);
        }

        return redirect()->route('cuadro-notas.index')->with('success', 'Notas registradas exitosamente.');
    }

    public function show($id)
    {
        $nota = CuadroNota::with(['estudiante', 'materia'])->findOrFail($id);
        return view('docentes.notas.show', compact('nota'));
    }

    public function edit($id)
    {
        $nota = CuadroNota::findOrFail($id);
        $materias = Materia::all();
        $estudiantes = Estudiante::all();
        $bimestres = Bimestre::all();

        return view('docentes.notas.edit', compact('nota', 'materias', 'estudiantes', 'bimestres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nota_final' => 'nullable|numeric|min:0|max:100',
            'nota_acumulada' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $nota = CuadroNota::findOrFail($id);
            $nota->update($request->all());

            return redirect()->route('cuadro-notas.index')->with('success', 'Nota actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al actualizar la nota.');
        }
    }

    public function destroy($id)
    {
        $nota = CuadroNota::findOrFail($id);
        $nota->delete();

        return redirect()->route('cuadro-notas.index')->with('success', 'Nota eliminada exitosamente.');
    }

    public function cierre(Request $request)
    {
        // Validar los parámetros
        $request->validate([
            'bimestre_id' => 'required|integer',
            'ciclo_escolar' => 'required|integer',
            'solicitud' => 'required|in:Si,No',
        ]);

        try {
            // Ejecutar la actualización
            $actualizados = DB::table('tb_cuadro_notas')
                ->where('bimestre', $request->bimestre_id)
                ->where('ciclo_escolar', $request->ciclo_escolar)
                ->update([
                    'cierre' => $request->solicitud,
                    'updated_at' => now()
                ]);

            // Respuesta de éxito


            return redirect()->route('cuadro_notas.mostrar_cierre')->with('success', 'Se procedio con la Solicitud Exitosamente');



        } catch (\Exception $e) {

            return redirect()->route('cuadro_notas.mostrar_cierre')->with('error', 'No se pudo realizar el Cierre Vuelva a Intentarlo');

        }
    }


    public function mostrarCierre()
    {
        // Obtenemos los bimestres y ciclos disponibles
        $bimestres = Bimestre::all();
        $anioActual = now()->year;
        // Calcular los años requeridos
        $anios = [
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
        ];

        return view('docentes.cierre.index', compact('bimestres', 'anios'));
    }

    public function notaspadres(Request $request)
    {


        $estudiante_id = $request->get('estudiante_id');
        $ciclo_escolar = $request->get('ciclo_escolar');
        $modo_promedio = $request->get('promedio');
        $incluirInsolventes = $request->has('incluir_insolventes'); // checkbox


        // 🔍 Verificar si el estudiante tiene cuotas pendientes con fecha vencida
        if (!$incluirInsolventes) {
            $tienePendiente = Cuota::whereHas('convenio.inscripcion', function ($q) use ($estudiante_id) {
                $q->where('estudiante_id', $estudiante_id);
            })
                ->where('estado', 'pendiente')
                ->whereDate('fecha_vencimiento', '<', now()) // ✅ Solo cuotas vencidas
                ->exists();

            if ($tienePendiente) {
                return back()->with([
                    'message' => 'El estudiante tiene pagos pendientes vencidos y no puede visualizar las notas.',
                    'message_type' => 'warning'
                ]);
            }
        }

        // 🔢 Cargar las notas si no tiene cuotas vencidas pendientes
        $notas = CuadroNota::with([
            'estudiante.persona',
            'estudiante.cgshges.grados',
            'estudiante.cgshges.cursos',
            'materia.gestionMateria',
            'bimestres'
        ])
            ->where('ciclo_escolar', $ciclo_escolar)
            ->where('estudiante_id', $estudiante_id)
            ->get();



        // 📄 Generar el reporte PDF de las notas
        $reporteController = new ReporteNotasController();
        return $reporteController->reporteBoleta($estudiante_id, $modo_promedio, $ciclo_escolar);
    }





    public function tareasrevisadas(Request $request)
    {
        $estudiante_id = $request->get('estudiante_id');
        $ciclo_escolar = $request->get('ciclo_escolar');

        $notas = CuadroNota::with(['estudiante', 'materia'])
            ->where('ciclo_escolar', $ciclo_escolar)
            ->where('estudiante_id', $estudiante_id)
            ->get();



        return view('docentes.notas.notasgeneral', compact('notas'));
    }

    public function buscarNotas()
    {

        $estudiantes = Estudiante::with('persona')->get(); // Ajusta según tu relación
        $ciclos = CuadroNota::select('ciclo_escolar')->distinct()->pluck('ciclo_escolar');


        return view('docentes.notas.buscar', compact('estudiantes', 'ciclos'));
    }

    public function mostrarestudiante()
    {

        $anioActual = now()->year;

        $anios = [
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
        ];

        $user = CMSUser::find(CRUDBooster::myId());
        $persona = $user->tareasAsignadas()->first();

        if (!$persona) {
            return redirect()->back()->with('error', 'No cuentas con Privilegios para acceder');
        }

        // Buscar la fila de la familia donde esté la persona como padre, madre o encargado
        $familia = Familia::where(function ($query) use ($persona) {
            $query->where('padre_persona_id', $persona->id)
                  ->orWhere('madre_persona_id', $persona->id)
                  ->orWhere('encargado_persona_id', $persona->id);
        })->first();

        if (!$familia) {
            return redirect()->back()->with('error', 'No cuentas con Privilegios para acceder');
        }

        // Obtener el código familiar
        $codigoFamiliar = $familia->codigo_familiar;

        // Buscar todas las filas de familia con ese mismo código familiar
        $familias = Familia::where('codigo_familiar', $codigoFamiliar)->get();

        // Obtener todos los estudiantes asociados
        $estudiantes = Estudiante::whereIn('id', $familias->pluck('estudiante_id'))->get();

        // Obtener inscripciones del año actual
        $inscripciones = Matriculacion::whereIn('estudiante_id', $estudiantes->pluck('id'))
            ->where('ciclo_escolar', $request->anio_ciclo_escolar)
            ->get();

        $bimestres = Bimestre::all();
        $ciclos = Gestion::all();


        // Retornar la vista con los estudiantes
        return view('docentes.notas.buscadorPadres', compact('estudiantes', 'bimestres', 'ciclos', 'anios'));
    }


    public function mostrarCalificacionEstudiante($estudiante_id, $tarea_id)
    {
        // Obtener el estudiante
        $estudiante = Estudiante::find($estudiante_id);
        if (!$estudiante) {
            return redirect()->back()->with('error', 'Estudiante no encontrado.');
        }

        // Obtener la calificación de la tarea para ese estudiante
        $calificacionTarea = CalificacionTarea::with(['tareaEstudiante.tarea'])
                            ->whereHas('tareaEstudiante', function ($q) use ($estudiante_id, $tarea_id) {
                                $q->where('estudiante_id', $estudiante_id)
                                  ->where('tarea_id', $tarea_id);
                            })
                            ->first();

        if (!$calificacionTarea) {
            return redirect()->back()->with('error', 'No se encontró calificación para esta tarea.');
        }


        // Pasar datos a la vista
        return view('docentes.notas.mostrarpunteodetareas', [
            'estudiante'      => $estudiante,
            'tarea'           => $calificacionTarea->tareaEstudiante->tarea,
            'calificacion'    => $calificacionTarea->calificacion,
            'comentario'      => $calificacionTarea->comentarios,
        ]);
    }



    public function showReportForm(Request $request)
    {
        $bimestres = Bimestre::all();
        $ciclosEscolares = Gestion::all();

        $user = CMSUser::find(CRUDBooster::myId());
        $persona = $user->tareasAsignadas()->first();

        if (!$persona) {
            abort(404, 'Persona no encontrada.');
        }

        $estudiante = Estudiante::with(['cgshges.materias.gestionMateria'])
                                ->where('persona_id', $persona->id)
                                ->firstOrFail();

        $inscripcion2 = Matriculacion::where('estudiante_id', $estudiante->id)
                            ->orderBy('id', 'desc')
                            ->with(['cgshges.materias.gestionMateria'])
                            ->first();

        $materiasPorCurso = $inscripcion2->cgshges->materias ?? collect();
        $estudiantes = $estudiante;

        // Filtrar calificaciones si se envió el formulario
        $calificaciones = collect();
        if ($request->all()) {
            $query = CalificacionTarea::with([
                'tareaEstudiante.estudiante.persona',
                'tareaEstudiante.tarea.materia.gestionMateria',
                'tareaEstudiante.tarea.bimestre'
            ]);

            if ($request->materia_id) {
                $query->whereHas('tareaEstudiante.tarea', function ($q) use ($request) {
                    $q->where('materia_id', $request->materia_id);
                });
            }

            if ($request->bimestre_id) {
                $query->whereHas('tareaEstudiante.tarea', function ($q) use ($request) {
                    $q->where('bimestre_id', $request->bimestre_id);
                });
            }

            if ($request->ciclo_escolar_id) {
                $query->whereHas('tareaEstudiante.tarea', function ($q) use ($request) {
                    $q->whereYear('created_at', $request->ciclo_escolar_id);
                });
            }

            $query->whereHas('tareaEstudiante', function ($q) use ($estudiante) {
                $q->where('estudiante_id', $estudiante->id);
            });

            $calificaciones = $query->get();
        }

        return view('docentes.notas.reportes', compact(
            'estudiantes',
            'materiasPorCurso',
            'bimestres',
            'ciclosEscolares',
            'calificaciones'
        ));
    }













    public function generateReport(Request $request)
    {
        $estudianteId = $request->input('estudiante_id');
        $materiaId = $request->input('materia_id');
        $bimestreId = $request->input('bimestre_id');
        $cicloEscolarId = $request->input('ciclo_escolar_id');

        $nombre = $request->input('nombre_estudiante');



        $query = CalificacionTarea::with([
            'tareaEstudiante.estudiante.persona',
            'tareaEstudiante.tarea.materia.gestionMateria',
            'tareaEstudiante.tarea.bimestre'
        ]);

        if ($estudianteId) {
            $query->whereHas('tareaEstudiante', function ($q) use ($estudianteId) {
                $q->where('estudiante_id', $estudianteId);
            });
        }


        if ($materiaId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            });
        }

        if ($bimestreId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($bimestreId) {
                $q->where('bimestre_id', $bimestreId);
            });
        }

        if ($cicloEscolarId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($cicloEscolarId) {
                $q->whereYear('created_at', $cicloEscolarId);
            });
        }

        if ($nombre) {
            $query->whereHas('tareaEstudiante.estudiante.persona', function ($q) use ($nombre) {
                $q->where(DB::raw("CONCAT(apellidos, ' ', nombres)"), 'LIKE', "%$nombre%");
            });
        }

        $calificaciones = $query->get();



        return view('docentes.notas.reporte', compact('calificaciones'));
    }



    public function showReportFormdocentes()
    {
        // $materias = Materia::all();
        // $docentes = Docente::all();
        $estados = Estado::all();
        $bimestreActual = Bimestre::actual()->first();
        $bimestres = Bimestre::all();
        $ciclos = Gestion::all();

        $userId = CRUDBooster::myId();

        // Verifica que el usuario exista antes de continuar
        if ($userId) {
            // Filtra las materias asignadas al docente autenticado
            $materias = Materia::with(['gestionMateria','cgshe'])
                ->whereHas('materiasCursos', function ($query) use ($userId) {
                    $query->whereHas('docente.persona', function ($subQuery) use ($userId) {
                        $subQuery->where('cms_users_id', $userId);
                    });
                })
                ->get();


            $docentes = Docente::with('persona')
            ->whereHas('materiasCursos', function ($query) use ($userId) {
                $query->whereHas('materia', function ($subQuery) use ($userId) {
                    $subQuery->whereHas('materiasCursos.docente.persona', function ($innerQuery) use ($userId) {
                        $innerQuery->where('cms_users_id', $userId);
                    });
                });
            })
            ->get();
        }


        //dd($materias);

        //dd($materias->first()->materiasCursos->first()->docente->first()->persona->nombres);

        return view('docentes.notas.reportesdocentes', compact('materias', 'ciclos', 'docentes', 'estados', 'bimestreActual', 'bimestres'));


    }


    public function generateReportdocentes(Request $request)
    {


        // Recibir los parámetros seleccionados

        //dd($request->all());
        $estudianteId = $request->input('estudiante_id');
        $materiaId = $request->input('materia_id');
        $bimestreId = $request->input('bimestre_id');
        $cicloEscolarId = $request->input('ciclo_escolar_id');

        $query = CalificacionTarea::with(['tareaEstudiante.tarea.materia', 'tareaEstudiante.tarea.estado']);

        if ($estudianteId) {
            $query->whereHas('tareaEstudiante', function ($q) use ($estudianteId) {

            });
        }

        if ($materiaId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            });
        }

        if ($bimestreId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($bimestreId) {
                $q->where('bimestre_id', $bimestreId);
            });
        }

        if ($cicloEscolarId) {
            $query->whereHas('tareaEstudiante.tarea', function ($q) use ($cicloEscolarId) {
                $q->whereYear('created_at', $cicloEscolarId);
            });
        }

        $calificaciones = $query->get();


        return view('docentes.notas.reporte', compact('calificaciones'));
    }
}
