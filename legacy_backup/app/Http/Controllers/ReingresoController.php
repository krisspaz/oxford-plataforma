<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Cgshge;
use App\Models\Estado;
use App\Models\Nivel;
use App\Models\Grado;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;
use App\Models\Matriculacion;
use App\Models\CMSUser;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

use App\Models\Paquete;

class ReingresoController extends Controller
{
    /**
     * Mostrar una lista de estudiantes elegibles para reingreso.
     */
    public function index()
    {
        $matriculaciones = Matriculacion::with(['estudiante', 'paquete', 'estado', 'cmsUser'])->get();
        $estudiantes = Estudiante::with(['persona', 'nivel', 'cgshges', 'estado', 'familia'])
            ->whereHas('estado', function ($query) {
                $query->where('estado', 'activo'); // Ajustar según el campo de estado
            })
            ->get();

        $anioActual = now()->year;

        // Calcular los años requeridos
        $anios = [

            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,

        ];

        $gestion = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->first(); // Usar `first` para obtener una sola gestión activa


        return view('reingreso.index', compact('estudiantes', 'matriculaciones', 'anios', 'gestion'));
    }

    /**
     * Mostrar el formulario de reingreso para un estudiante específico.
     */
    public function edit(Request $request, $id)
    {

        $matriculaciones = Matriculacion::with(['estudiante', 'paquete', 'estado', 'cmsUser'])->get();


        $estudiante = Estudiante::with(['persona', 'nivel', 'cgshges', 'estado', 'familia'])
            ->findOrFail($id);

        $estados = Estado::all();

        $niveles = Nivel::all();
        $grados = Cgshge::all();

        // Verificar cuotas pendientes
        $cuotasPendientes = $estudiante->cuotasPendientes()->count();

        if ($cuotasPendientes > 0 && !$request->has('continuar')) {
            return back()->with([
                'error' => "⚠️ El estudiante tiene $cuotasPendientes cuotas pendientes. Favor verificar antes de inscribir.",
                'continuar' => true,
                'estudiante_id' => $estudiante->id,
            ]);
        }

        $ngestiones = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->get();


        //dd($ngestiones);


        $paquetes = Paquete::all();

        // Obtener el año actual
        $anioActual = now()->year;

        // Calcular los años requeridos
        $anios = [

            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,

        ];
        //dd($ngestiones);


        return view('reingreso.edit', compact('estados', 'estudiante', 'niveles', 'grados', 'ngestiones', 'matriculaciones', 'paquetes', 'anios'));
    }




    /**
     * Actualizar los datos del estudiante para realizar el reingreso.
     */
    public function update(Request $request, $id)
    {


        $user = CMSUser::find(CRUDBooster::myId());
        $request->validate([
             'nivel_id' => 'exists:niveles,id',

             'estado_id' => 'exists:tb_estados,id',

             'paquete_id' => 'exists:paquetes,id',
             'fecha_inscripcion' => 'date',
         ]);

        $asignacionid = $this->getPvCgshgeId(
            $request->input('gestion_id'),
            $request->input('nivel_id'),
            $request->input('curso_id'),
            $request->input('grado_id'),
            $request->input('seccion_id'),
            $request->input('jornada_id')
        );

        //dd($asignacionid);


        $estudiante = Estudiante::findOrFail($id);

        $estudiante->update([

            'cgshges_id' => $asignacionid,
            'estado_id' => $request->estado_id,
        ]);


        $matriculacion =Matriculacion::create([
         'estudiante_id' => $estudiante->id,
         'cgshges_id' =>  $asignacionid,
         'paquete_id' => $request->paquete_id,
         'fecha_inscripcion' => $request->fecha_inscripcion,
         'ciclo_escolar' => $request->ciclo_escolar,
         'estado_id' => $request->estado_id,
         'cms_users_id' =>  $user->id,
        ]);








        $matriculaciones = Matriculacion::with(['estudiante', 'paquete', 'estado', 'cmsUser'])->get();
        $estudiantes = Estudiante::with(['persona', 'nivel', 'cgshges', 'estado', 'familia'])
            ->whereHas('estado', function ($query) {
                $query->where('estado', 'activo'); // Ajustar según el campo de estado
            })
            ->get();

        //  dd( $matriculacion->estudiante->id);
        // dd($id);
        $contratoController = new ContratoController();
        $contratoController->generarContrato($matriculacion);




        return view('inscripcion.index');




    }




    public function getPvCgshgeId($gestionId, $nivelId, $cursoId, $gradoId, $seccionId, $jornadaId)
    {
        $pvCgshge = Cgshge::where('gestion_id', $gestionId)
            ->where('nivel_id', $nivelId)
            ->where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('seccion_id', $seccionId)
            ->where('jornada_id', $jornadaId)
            ->first(); // Obtiene el primer registro que coincida con los parámetros

        return $pvCgshge ? $pvCgshge->id : null; // Si se encuentra el registro, devuelve el ID, sino null
    }


    public function getNiveles(Request $request)
    {
        $gestionId = $request->input('gestion_id');

        if (!$gestionId) {
            return response()->json(['error' => 'El gestion_id es requerido.'], 400);
        }

        $niveles = DB::table('pv_cgshges')
        ->join('niveles', 'pv_cgshges.nivel_id', '=', 'niveles.id')
        ->where('pv_cgshges.gestion_id', $gestionId)
        ->whereNotNull('niveles.nivel')
        ->distinct()
        ->select('niveles.id', 'niveles.nivel')
        ->get();

        return response()->json($niveles);
    }

    // Obtener cursos basados en el nivel seleccionado
    public function getCursos(Request $request)
    {
        $nivelId = $request->input('nivel_id');

        if (!$nivelId) {
            return response()->json(['error' => 'El nivel_id es requerido.'], 400);
        }

        $cursos = DB::table('pv_cgshges')
        ->join('cursos', 'pv_cgshges.curso_id', '=', 'cursos.id')  // JOIN entre las tablas
        ->where('pv_cgshges.nivel_id', $nivelId)  // Filtrar por nivel_id = 6
        ->whereNotNull('cursos.curso')  // Asegura que el curso no sea NULL
        ->distinct()  // Elimina duplicados
        ->select('cursos.id', 'cursos.curso')  // Selecciona los campos requeridos
        ->get();  // Obtiene los resultados

        return response()->json($cursos);  // Devuelve los resultados como JSON
    }

    // Obtener grados basados en el curso seleccionado
    public function getGrados(Request $request)
    {
        $cursoId = $request->input('curso_id');

        if (!$cursoId) {
            return response()->json(['error' => 'El curso_id es requerido.'], 400);
        }

        $grados = DB::table('pv_cgshges')
        ->join('tb_grados', 'pv_cgshges.grado_id', '=', 'tb_grados.id')
        ->where('pv_cgshges.curso_id', $cursoId)
        ->whereNotNull('tb_grados.nombre')
        ->distinct()
        ->select('tb_grados.id', 'tb_grados.nombre')
        ->get();


        return response()->json($grados);
    }

    // Obtener secciones basadas en el grado seleccionado
    public function getSecciones(Request $request)
    {
        $cursoId = $request->input('curso_id');
        $gradoId = $request->input('grado_id');

        if (!$cursoId) {
            return response()->json(['error' => 'El grado_id es requerido.'], 400);
        }

        $secciones = DB::table('pv_cgshges')
        ->join('secciones', 'pv_cgshges.seccion_id', '=', 'secciones.id')
        ->where('pv_cgshges.curso_id', $cursoId)
        ->where('pv_cgshges.grado_id', $gradoId)
        ->whereNotNull('secciones.seccion')
        ->distinct()
        ->select('secciones.id', 'secciones.seccion')
        ->get();



        return response()->json($secciones);
    }

    // Obtener jornadas basadas en la sección seleccionada
    public function getJornadas(Request $request)
    {
        $seccionId = $request->input('seccion_id');

        if (!$seccionId) {
            return response()->json(['error' => 'El curso_id es requerido.'], 400);
        }

        $jornadas = DB::table('pv_cgshges')
        ->join('cursos', 'pv_cgshges.curso_id', '=', 'cursos.id')
        ->join('pv_jornada_dia_horarios', 'pv_cgshges.jornada_id', '=', 'pv_jornada_dia_horarios.id')
        ->join('tb_jornadas', 'pv_jornada_dia_horarios.jornada_id', '=', 'tb_jornadas.id')
        ->where('pv_cgshges.seccion_id', $seccionId)
        ->whereNotNull('tb_jornadas.nombre')
        ->distinct()
        ->select('tb_jornadas.id', 'tb_jornadas.nombre')
        ->get();



        return response()->json($jornadas);
    }




    public function getPaquetes(Request $request)
    {
        $cursoId = $request->curso_id;

        if (!$cursoId) {
            return response()->json(['error' => 'El curso_id es requerido para el Paquete.'], 400);
        }


        // Obtiene los paquetes vinculados al curso a través de la tabla pivote
        $paquetes = Paquete::whereHas('cursos', function ($query) use ($cursoId) {
            $query->where('curso_id', $cursoId);
        })->get(['id', 'nombre']);


        return response()->json($paquetes);
    }





}
