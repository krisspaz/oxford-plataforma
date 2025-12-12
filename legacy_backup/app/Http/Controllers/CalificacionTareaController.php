<?php

namespace App\Http\Controllers;

use App\Models\CalificacionTarea;
use App\Models\Tarea;
use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Familia;
use App\Models\Gestion;
use App\Models\TareaEstudiante;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CmsNotification;

use App\Models\CMSUser;
use App\Models\Bimestre;
use App\Models\Matriculacion;

use Carbon\Carbon;
use App\Models\Materia;
use App\Models\Docente;

use App\Models\Persona;
use CRUDBooster;

use DB;

class CalificacionTareaController extends Controller
{
    /**
     * Mostrar el formulario para crear una calificación de tarea.
     */


    public function index(Request $request)
    {
        // Limpiar filtros
        if ($request->has('limpiar')) {
            session()->forget(['tareas_filtro_buscar', 'tareas_filtro_bimestre']);
            return redirect()->route('calificaciones.index');
        }

        // Guardar o recuperar filtros en sesión
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
        $bimestreFiltro = $request->input('bimestre');

        $userId = CRUDBooster::myId();
        $añoActual = now()->year;

        $user = CMSUser::find($userId);
        $persona = Persona::where('cms_users_id', $userId)->first();
        $docenteId = $persona ? Docente::where('persona_id', $persona->id)->value('id') : null;

        // Obtener el ID del estado "Activo"
        $estadoActivo = Estado::where('estado', 'Activo')->first();
        // Obtener ID del estado "Activo"
        $estadoActivoId = Estado::where('estado', 'Activo')->value('id');

        $cuadroNotas = CuadroNota::with([
                'estudiante.cgshges.grados',
                'materia.gestionMateria',
                'estudiante.persona',
                'bimestres'
            ])
            ->where('ciclo_escolar', $añoActual)
            ->whereYear('created_at', $añoActual)
            ->whereHas('bimestres', function ($query) use ($estadoActivoId) {
                $query->whereHas('gestion', function ($q) use ($estadoActivoId) {
                    $q->where('estado_id', $estadoActivoId);
                });
            })
            ->get();


        // Filtro por rol
        $privilegiosPermitidos = ['administrativo', 'secretaria', 'super administrador'];
        if (!in_array(strtolower($user->cmsPrivilege->name), $privilegiosPermitidos)) {
            $query->when($docenteId, function ($q) use ($docenteId) {
                $q->where('docente_id', $docenteId);
            }, function ($q) {
                $q->whereNull('id'); // Si no es docente y no tiene ID, no retorna nada
            });
        }

        // Filtro de búsqueda
        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhereHas('materia.gestionMateria', function ($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtro por bimestre
        if (!empty($bimestreFiltro)) {
            $query->whereHas('bimestre', function ($q) use ($bimestreFiltro) {
                $q->where('nombre', $bimestreFiltro);
            });
        }

        // Traer resultados finales
        $tareas = $query->get();


        // Bimestres activos para el select
        $bimestres = Bimestre::whereHas('gestion.estado', function ($q) use ($estadoActivoId) {
            $q->where('estado_id', $estadoActivoId);
        })->get();

        return view('calificaciones.index', [
            'tareas' => $tareas,
            'buscar' => $buscar,
            'bimestres' => $bimestres,
            'bimestreFiltro' => $bimestreFiltro,
        ]);
    }







    public function create(TareaEstudiante $tareaEstudianteId)
    {

        $tareasEstudiantes = TareaEstudiante::paginate(50);


        return view('calificaciones.create', compact('tareaEstudiantes'));
    }

    /**
     * Almacenar una calificación para una tarea.
     */
    public function store(Request $request)
    {

        // Validar los datos recibidos
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',  // Calificación entre 0 y 100
            'comentarios' => 'nullable|string|max:500',
        ]);



        // Buscar la relación de tarea-estudiante
        $tareaEstudiante = TareaEstudiante::findOrFail($request->tarea_estudiante_id);



        // Crear una nueva calificación de tarea
        CalificacionTarea::create([
            'tarea_estudiante_id' => $tareaEstudiante->id,
            'calificacion' => $request->input('calificacion'),
            'comentarios' => $request->input('comentarios', null),
        ]);

        // Verificar qué botón fue presionado
        if ($request->input('action') === 'continuar') {
            return redirect()->route('calificaciones.calificar', ['tarea' => $tareaEstudiante->tarea_id])
                ->with('success', 'Calificación almacenada. Puedes continuar.');
        }

        return redirect()->route('calificaciones.index')->with('success', 'Calificación almacenada correctamente.');



    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'tarea_estudiante_id' => 'required|array',
            'calificaciones'     => 'required|array',
            'calificaciones.*'   => 'numeric|min:0|max:100',
            'comentarios'        => 'nullable|array',
            'comentarios.*'      => 'nullable|string|max:500',
        ]);

        $ciclo_escolar = date('Y');
        $materia_id = $request->materia_id;
        $bimestre_id = $request->bimestre;

        foreach ($request->tarea_estudiante_id as $index => $tareaEstudianteId) {
            // Guardar o actualizar calificación
            $calificacion = CalificacionTarea::updateOrCreate(
                ['tarea_estudiante_id' => $tareaEstudianteId],
                [
                    'calificacion' => $request->calificaciones[$index],
                    'comentarios'  => $request->comentarios[$index] ?? null,
                ]
            );



            // --- ENVÍO DE NOTIFICACIÓN ---
            $tareaEstudiante = $calificacion->tareaEstudiante; // relación
            $estudiante = $tareaEstudiante->estudiante;        // relación con estudiante

            if ($estudiante && $estudiante->persona && $estudiante->persona->cmsUser) {
                $usuario_estudiante_id = $estudiante->persona->cmsUser->id;

                $mensaje = "Se ha calificado la tarea '{$tareaEstudiante->tarea->titulo}'";

                // URL directa a la calificación
                $urlNotificacion = route('cuadro-nota.mostrarCalificacion', [
                    'estudiante_id' => $estudiante->id,
                    'tarea_id'      => $tareaEstudiante->tarea->id
                ]);

                // Crear notificación usando modelo
                CmsNotification::create([
                    'content'      => $mensaje,
                    'id_cms_users' => $usuario_estudiante_id, // columna correcta en v5
                    'url'          => $urlNotificacion,
                    'is_read'      => 0,
                ]);
            }
            // --- FIN NOTIFICACIÓN ---
        }

        // Obtener los estudiantes únicos enviados
        $estudiantesUnicos = array_unique($request->estudiante_id ?? []);

        // Sumar calificaciones por cada estudiante
        foreach ($estudiantesUnicos as $estudiante_id) {
            $this->sumarCalificaciones($estudiante_id, $materia_id, $bimestre_id, $ciclo_escolar);
        }

        return redirect()->route('calificaciones.index')
            ->with('success', 'Todas las calificaciones han sido guardadas y notificaciones enviadas.');
    }



    /**
     * Mostrar el formulario para editar la calificación de una tarea.
     */
    public function edit($id)
    {
        // Buscar la calificación de tarea
        $calificacion = CalificacionTarea::findOrFail($id);



        return view('calificaciones.edit', compact('calificacion'));
    }

    /**
     * Actualizar una calificación para una tarea.
     */
    public function update(Request $request, $id)
    {


        // Validar los datos recibidos
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',  // Calificación entre 0 y 10
            'comentarios' => 'nullable|string|max:500',
        ]);

        // Buscar la calificación de tarea
        $calificacion = CalificacionTarea::findOrFail($id);

        // Actualizar la calificación de tarea
        $calificacion->update([
            'calificacion' => $request->input('calificacion'),
            'comentarios' => $request->input('comentarios', null),
        ]);

        return redirect()->route('calificaciones.index')->with('success', 'Calificación actualizada correctamente.');
    }

    /**
     * Eliminar una calificación de tarea.
     */
    public function destroy($id)
    {
        // Buscar la calificación de tarea
        $calificacion = CalificacionTarea::findOrFail($id);

        // Eliminar la calificación
        $calificacion->delete();

        return redirect()->route('calificaciones.index')->with('success', 'Calificación eliminada correctamente.');
    }

    public function calificar($tareaId)
    {
        // Buscar la tarea
        $tarea = Tarea::findOrFail($tareaId);

        // Obtener las tareas subidas por los estudiantes
        $tareasEstudiantes = TareaEstudiante::where('tarea_id', $tareaId)
        ->whereDoesntHave('calificacion')
            ->with('estudiante')
            ->get();

        $tareasEstudiantes = $tareasEstudiantes->sortBy(function ($item) {
            return strtolower($item->estudiante->persona->apellidos . ' ' . $item->estudiante->persona->nombres);
        });


        return view('calificaciones.calificar', compact('tarea', 'tareasEstudiantes'));
    }

    public function calificadas($tareaId)
    {
        // Buscar la tarea específica
        $tarea = Tarea::findOrFail($tareaId);

        // Obtener las tareas de estudiantes que ya tienen calificación
        $tareasCalificadas = TareaEstudiante::where('tarea_id', $tareaId)
            ->whereHas('calificacion') // Solo aquellas que tienen una relación de calificación
            ->with(['estudiante', 'calificacion'])
            ->get();

        $tareasCalificadas2 = TareaEstudiante::where('estudiante_id', '1') // Filtrar por estudiante
    ->with(['tarea', 'estudiante.persona', 'calificacion']) // Cargar relaciones
    ->get();

        // dd($tareasCalificadas2);

        $tareasCalificadas = $tareasCalificadas->sortBy(function ($item) {
            return strtolower($item->estudiante->persona->apellidos . ' ' . $item->estudiante->persona->nombres);
        });

        return view('calificaciones.calificadas', compact('tarea', 'tareasCalificadas'));
    }

    public function verNotas()
    {

        $tareasCalificadas = TareaEstudiante::with(['tarea', 'estudiante', 'calificacion']) // Cargar relaciones
    ->get();

    }

    public function mostrarFormulario()
    {
        // Obtener el usuario autenticado
        $user = CMSUser::find(CRUDBooster::myId());

        // Obtener la persona asociada al usuario autenticado
        $persona = $user->tareasAsignadas()->first();

        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró la persona asociada a este usuario.');
        }

        // Obtener la familia asociada a la persona (padre, madre o encargado)
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
        // Obtener bimestres y ciclos
        $bimestres = Bimestre::all();
        $ciclos = Gestion::all();

        // Obtener materias asociadas a cada estudiante (opcional: puedes adaptarlo si solo mostrarás uno)
        $materias = collect();
        foreach ($estudiantes as $estudiante) {
            $materiasEstudiante = $estudiante->gestionMateriasDesdeTareas();
            $materias = $materias->merge($materiasEstudiante);
        }

        // Eliminar duplicados (por id de la materia)
        $materias = $materias->unique('id')->values();

        // Retornar la vista
        return view('calificaciones.formulario', compact('estudiantes', 'bimestres', 'ciclos', 'materias'));


    }

    // Método para buscar las tareas calificadas por estudiante
    public function buscarTareas(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'bimestre' => 'nullable|exists:bimestres,id',
            'materia_id' => 'nullable|exists:materias,id',
            'anio_ciclo_escolar' => 'required'
        ]);

        // Obtener el estudiante seleccionado
        $estudiante = Estudiante::whereHas('inscripciones', function ($query) use ($request) {
            $query->where('ciclo_escolar', $request->anio_ciclo_escolar);
        })->find($request->estudiante_id);

        if (!$estudiante) {
            return redirect()->back()->with('error', 'El estudiante no está inscrito en el ciclo escolar seleccionado.');
        }


        $pruebaestudiante = $estudiante->tareaEstudiantes;

        // Realizamos la consulta para obtener las tareas calificadas
        $tareasCalificadas = $estudiante->tareaEstudiantes()
         ->with(['tarea.bimestre', 'tarea.materia.gestionMateria', 'calificacion'])
         ->whereHas('tarea', function ($query) use ($request) {
             if (!empty($request->bimestre_id)) {
                 $query->where('bimestre_id', $request->bimestre_id);
             }

         })
         ->get();




        // Verificar si no se encontraron tareas
        if ($tareasCalificadas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron tareas con los criterios seleccionados.');
        }

        // dd($request->all(),$tareasCalificadas);
        // Retornar la vista con las tareas calificadas


        return view('calificaciones.resultados', compact('tareasCalificadas', 'estudiante', 'anio_ciclo_escolar'));
    }



    public function descargarPDF(Request $request, Estudiante $estudiante)
    {
        // Parámetros opcionales desde la URL
        $bimestre_id = $request->query('bimestre_id');
        $materia_id = $request->query('materia_id');
        $docente_id = $request->query('docente_id');

        //dd($request->all());
        $anio_ciclo_escolar = $request->query('anio_ciclo_escolar', now()->year); // usa el actual si no se pasa

        // Consulta con filtros
        $tareasCalificadas = $estudiante->tareaEstudiantes()
            ->with([
                'tarea.bimestre',
                'tarea.materia.gestionMateria',
                'tarea.docente.persona',
                'calificacion'
            ])
            ->whereHas('tarea', function ($query) use ($bimestre_id, $materia_id, $docente_id) {
                if (!empty($bimestre_id)) {
                    $query->where('bimestre_id', $bimestre_id);
                }
                if (!empty($materia_id)) {
                    $query->where('materia_id', $materia_id);
                }
                if (!empty($docente_id)) {
                    $query->where('docente_id', $docente_id);
                }
            })
            ->get();

        // Generar el PDF
        $pdf = \PDF::loadView('reportes_academicos.tareas_calificadas', compact(
            'tareasCalificadas',
            'estudiante',
            'anio_ciclo_escolar'
        ))->setPaper('Carta', 'portrait');

        return $pdf->download('tareas_calificadas.pdf');
    }



    public function mostrarFormularioGuia()
    {


        // Obtener el usuario autenticado
        $user = CMSUser::find(CRUDBooster::myId());

        // Obtener la persona asociada al usuario autenticado
        $persona = $user->tareasAsignadas()->first();

        if ($persona) {
            // Obtener la familia asociada a la persona (padre, madre o encargado)
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

            $inscripciones = Matriculacion::whereIn('estudiante_id', $estudiantes->pluck('id'))
              ->where('ciclo_escolar', $request->anio_ciclo_escolar)
              ->get();


        } else {
            // dd('No se encontró la persona asociada a este usuario.');
            return redirect()->back()->with('error', 'No cuentas con Privilegios para acceder');
        }

        $bimestres = Bimestre::all();
        $ciclos = Gestion::all();




        // Retornar la vista con los estudiantes
        return view('calificaciones.formulariobuscadorguia', compact('estudiantes', 'bimestres', 'ciclos'));
    }



    public function BuscarGuia(Request $request)
    {
        $request->validate([
           'estudiante_id' => 'required|exists:estudiantes,id',
           'anio_ciclo_escolar' => 'required'
    ]);

        $estudiante = Estudiante::whereHas('inscripciones', function ($query) use ($request) {
            $query->where('ciclo_escolar', $request->anio_ciclo_escolar);
        })->with(['inscripciones' => function ($q) use ($request) {
            $q->where('ciclo_escolar', $request->anio_ciclo_escolar)
              ->orderBy('id', 'desc')
              ->with(['cgshges.materias.gestionMateria']); // Aquí se carga correctamente
        }])->find($request->estudiante_id);

        if (!$estudiante) {
            return redirect()->back()->with('error', 'El estudiante no está inscrito en el ciclo escolar seleccionado.');
        }




        $inscripcion2 = Matriculacion::where('estudiante_id', $request->estudiante_id)
        ->orderBy('id', 'desc')
        ->with(['cgshges.materias.gestionMateria']) // Cargar materias y gestión
        ->first();

        $materias = $inscripcion2->cgshges->materias ?? collect();

        $tareasCalificadas = $estudiante->tareaEstudiantes()
            ->with(['tarea.bimestre', 'tarea.materia.gestionMateria', 'tarea.docente.persona'])
            ->whereHas('tarea.bimestre.gestion', function ($query) use ($request) {
                $query->where('ciclo_escolar', $request->anio_ciclo_escolar);
            })
            ->get();


        $bimestres = Bimestre::orderBy('id')->get();
        $ultimoBimestre = $bimestres->last()->id; // obtiene el ID del último bimestre


        $anio_ciclo_escolar = $request->anio_ciclo_escolar;

        return view('calificaciones.resultadostareas', compact(
            'tareasCalificadas',
            'estudiante',
            'anio_ciclo_escolar',
            'bimestres',
            'ultimoBimestre',
            'materias'
        ));
    }


    public function sumarCalificaciones($estudiante_id, $materia_id, $bimestre_id, $ciclo_escolar)
    {
        $totalCalificacion = DB::table('tb_calificaciones_tareas as ct')
            ->join('tb_tarea_estudiantes as te', 'ct.tarea_estudiante_id', '=', 'te.id')
            ->join('tb_tareas as t', 'te.tarea_id', '=', 't.id')
            ->where('t.materia_id', $materia_id)
            ->where('t.bimestre_id', $bimestre_id)
            ->where('te.estudiante_id', $estudiante_id) // Filtrar por estudiante
            ->sum('ct.calificacion');

        // Buscar si ya existe el registro para este estudiante
        $cuadroNota = DB::table('tb_cuadro_notas')
            ->where('estudiante_id', $estudiante_id) // <<<<<< Agregado
            ->where('materia_id', $materia_id)
            ->where('bimestre', $bimestre_id)
            ->where('ciclo_escolar', $ciclo_escolar)
            ->first();

        if ($cuadroNota) {
            // Actualizar
            DB::table('tb_cuadro_notas')
                ->where('estudiante_id', $estudiante_id)
                ->where('materia_id', $materia_id)
                ->where('bimestre', $bimestre_id)
                ->where('ciclo_escolar', $ciclo_escolar)
                ->update([
                    'nota_final' => $totalCalificacion,
                    'nota_acumulada' => $totalCalificacion,
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            // Insertar
            DB::table('tb_cuadro_notas')->insert([
                'estudiante_id' => $estudiante_id,
                'materia_id' => $materia_id,
                'bimestre' => $bimestre_id,
                'nota_final' => $totalCalificacion,
                'nota_acumulada' => $totalCalificacion,
                'ciclo_escolar' => $ciclo_escolar,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'message' => 'La suma de las calificaciones se ha actualizado correctamente.',
            'total_calificacion' => $totalCalificacion
        ]);
    }











}
