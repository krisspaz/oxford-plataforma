<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\GestionMateria;
use App\Models\Cgshge;
use App\Models\Estado;
use App\Models\Jornada;
use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\Grado;
use App\Models\Curso;
use App\Models\Seccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Mostrar una lista de las materias.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Verifica si el checkbox está marcado
        $incluirInactivas = $request->has('incluir_inactivas');

        // Obtener ID del estado "Activo"
        $estadoActivo = Estado::whereRaw('LOWER(estado) = ?', ['activo'])->first();

        // Consulta base con relaciones
        $materiasQuery = Materia::with(['gestionMateria', 'cgshe', 'estado']);

        if (!$incluirInactivas && $estadoActivo) {
            // Solo mostrar activas si el checkbox NO está marcado
            $materiasQuery->where('estado_id', $estadoActivo->id);
        }

        $materias = $materiasQuery->get();

        return view('materias.index', compact('materias', 'incluirInactivas'));
    }

    /**
     * Muestra el formulario para crear una nueva materia.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gestionMaterias = GestionMateria::all();
        $cgshes = Cgshge::all();
        $estados = Estado::all();

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

        $niveles = Nivel::all();
        $grados = Cgshge::all();

        $ngestiones = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->get();


        return view('materias.create', compact('gestionMaterias', 'cgshes', 'estados', 'niveles', 'grados', 'ngestiones', 'matriculaciones', 'paquetes', 'anios'));
    }

    /**
     * Almacenar una nueva materia en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:gestion_materias,id',

            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $asignacionid = $this->getPvCgshgeId(
            $request->input('gestion_id'),
            $request->input('nivel_id'),
            $request->input('curso_id'),
            $request->input('grado_id'),
            $request->input('seccion_id'),
            $request->input('jornada_id')
        );

        Materia::create([
            'materia_id' => $request->materia_id,
            'cgshe_id' => $asignacionid,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()->route('materias.index')->with('success', 'Materia creada exitosamente');
    }

    /**
     * Muestra el formulario para editar la materia especificada.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function edit(Materia $materia)
    {
        $gestionMaterias = GestionMateria::all();

        // $materia = Materia::findOrFail($$materia->id);

        //dd($materia->cgshe->gestiones->gestion);
        $estados = Estado::all();

        $niveles = Nivel::all();
        $grados = Cgshge::all();
        $cursos= Curso::all();
        $grados = Grado::all();
        $secciones = Seccion::all();
        $jornadas = Jornada::all();

        $ngestiones = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->get();



        return view('materias.edit', compact('materia', 'gestionMaterias', 'secciones', 'jornadas', 'grados', 'cursos', 'cgshes', 'estados', 'niveles', 'grados', 'ngestiones', 'matriculaciones', 'paquetes', 'anios'));
    }

    /**
     * Actualiza la materia especificada en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'materia_id' => 'required|exists:gestion_materias,id',

            'estado_id' => 'required|exists:tb_estados,id',
        ]);

        $asignacionid = $this->getPvCgshgeId(
            $request->input('gestion_id'),
            $request->input('nivel_id'),
            $request->input('curso_id'),
            $request->input('grado_id'),
            $request->input('seccion_id'),
            $request->input('jornada_id')
        );

        $materia->update([
            'materia_id' => $request->materia_id,
            'cgshe_id' =>  $asignacionid,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()->route('materias.index')->with('success', 'Materia actualizada exitosamente');
    }

    /**
     * Elimina la materia especificada de la base de datos.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('materias.index')->with('success', 'Materia eliminada exitosamente');
    }

    /**
     * Muestra la información de una materia especificada.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function show(Materia $materia)
    {


        return view('materias.show', compact('materia'));
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

}
