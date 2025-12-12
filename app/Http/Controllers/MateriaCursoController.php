<?php

namespace App\Http\Controllers;

use App\Models\MateriaCurso;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\GestionMateria;
use App\Models\Curso;
use App\Models\Estado;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\Jornada;
use App\Models\Cgshge;
use App\Models\Nivel;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MateriaCursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materiasCursos = MateriaCurso::with(['docente', 'materia', 'cgshges', 'estado'])
         ->whereHas('estado', function ($query) {
             $query->where('estado', 'Activo');
         })
         ->whereHas('materia.estado', function ($query) {
             $query->where('estado', 'Activo');
         })
         ->get();

        return view('materiascursos.index', compact('materiasCursos'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $docentes = Docente::all();
        $materias = Materia::all();
        $estados = Estado::all();
        $niveles = Nivel::all();
        $ngestiones = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->get();


        return view('materiascursos.create', compact(
            'ngestiones',
            'niveles',
            'docentes',
            'materias',
            'estados'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'docente_id' => 'required|exists:docentes,id',

            'estado_id' => 'required|exists:tb_estados,id',
            'materias' => 'required|array',
            'materias.*' => 'exists:materias,id',
        ]);

        $asignacionid = $this->getPvCgshgeId(
            $request->input('gestion_id'),
            $request->input('nivel_id'),
            $request->input('curso_id'),
            $request->input('grado_id'),
            $request->input('seccion_id'),
            $request->input('jornada_id')
        );

        // dd( $asignacionid);

        foreach ($validatedData['materias'] as $materiaId) {
            MateriaCurso::create([
                'docente_id' => $validatedData['docente_id'],
                'estado_id' => $validatedData['estado_id'],
                'materia_id' => $materiaId,
                'gshges_id'  => $asignacionid,

            ]);
        }

        return redirect()->route('materiascursos.index')->with('success', 'Asignación creada exitosamente.');
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


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return view('materiascursos.show', compact('materiasCurso'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // 🔹 Obtén el docente con el ID proporcionado
        $docente = Docente::findOrFail($id);

        // 🔹 Obtén los IDs de materias activas
        $materiasActivasIds = Materia::whereHas('estado', function ($query) {
            $query->where('estado', 'Activo');
        })->pluck('id');

        // 🔹 Obtén las materias_cursos activas relacionadas al docente
        $materiasAsignadas = MateriaCurso::where('docente_id', $docente->id)
            ->whereHas('estado', function ($query) {
                $query->where('estado', 'Activo');
            })
            ->whereIn('materia_id', $materiasActivasIds)
            ->pluck('materia_id')
            ->toArray();

        // 🔹 Obtén una asignación del docente para usar su estructura
        $materiascurso = MateriaCurso::where('docente_id', $docente->id)
            ->whereHas('estado', function ($query) {
                $query->where('estado', 'Activo');
            })
            ->first();



        // 🔹 Catálogos generales
        $docentes = Docente::all();
        $materias = Materia::whereHas('estado', function ($query) {
            $query->where('estado', 'Activo');
        })->get();

        $estados = Estado::all();
        $niveles = Nivel::all();
        $grados = Grado::all();
        $cursos = Curso::all();
        $secciones = Seccion::all();
        $jornadas = Jornada::all();

        // 🔹 Solo gestiones activas
        $ngestiones = DB::table('gestionesacademicas')
            ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
            ->where(function ($query) {
                $query->where('tb_estados.estado', 'Activo')
                      ->orWhere('tb_estados.estado', 'ACTIVO');
            })
            ->select('gestionesacademicas.*')
            ->get();

        return view('materiascursos.edit', compact(
            'docente',
            'materias',
            'cursos',
            'jornadas',
            'grados',
            'secciones',
            'niveles',
            'ngestiones',
            'estados',
            'materiasAsignadas',
            'materiascurso'
        ));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $docenteId)
    {
        // Validación de los datos enviados desde el formulario
        $validatedData = $request->validate([

            'estado_id' => 'required|exists:tb_estados,id',
            'materias' => 'array', // Asegúrate de que al menos una materia esté seleccionada
        ]);

        // Obtener los valores validados


        $asignacionid = $this->getPvCgshgeId(
            $request->input('gestion_id'),
            $request->input('nivel_id'),
            $request->input('curso_id'),
            $request->input('grado_id'),
            $request->input('seccion_id'),
            $request->input('jornada_id')
        );




        $estadoId = $validatedData['estado_id'];
        $materiasSeleccionadas = $validatedData['materias'] ?? [];

        // Obtén las asignaciones existentes de materias para el docente en el curso especificado
        $materiasAsignadas = MateriaCurso::where('docente_id', $docenteId)
            ->where('gshges_id', $asignacionid)
            ->pluck('materia_id')
            ->toArray();

        // Agregar nuevas materias seleccionadas
        $nuevasMaterias = array_diff($materiasSeleccionadas, $materiasAsignadas);
        foreach ($nuevasMaterias as $materiaId) {
            MateriaCurso::create([
                'docente_id' => $docenteId,
                'gshges_id' => $asignacionid,
                'materia_id' => $materiaId,
                'estado_id' => $estadoId,
            ]);
        }

        // Eliminar materias desmarcadas
        $materiasEliminar = array_diff($materiasAsignadas, $materiasSeleccionadas);
        MateriaCurso::where('docente_id', $docenteId)
            ->where('gshges_id', $asignacionid)
            ->whereIn('materia_id', $materiasEliminar)
            ->delete();

        // Actualizar los campos adicionales de curso, grado, sección, jornada y estado para todas las materias asignadas al docente
        MateriaCurso::where('docente_id', $docenteId)
            ->where('gshges_id', $asignacionid)
            ->update([
                'gshges_id' => $asignacionid,

                'estado_id' => $estadoId,
            ]);

        // Redirigir con mensaje de éxito
        return redirect()->route('materiascursos.index')->with('success', 'Asignación actualizada correctamente.');
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($docenteId)
    {
        \Log::info("Eliminando todas las asignaciones de cursos para el docente con ID: " . $docenteId);

        // Buscar y eliminar todas las asignaciones de cursos relacionadas con el docente
        MateriaCurso::where('docente_id', $docenteId)->delete();

        return redirect()->route('materiascursos.index')->with('success', 'Todas las asignaciones del docente han sido eliminadas exitosamente.');
    }

    //funciones

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


    public function getMateriasByCurso($gestionId, $nivelId, $cursoId, $gradoId, $seccionId, $jornadaId)
    {
        // Buscar el registro en la tabla `Cgshge`
        $pvCgshge = Cgshge::where('gestion_id', $gestionId)
            ->where('nivel_id', $nivelId)
            ->where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('seccion_id', $seccionId)
            ->where('jornada_id', $jornadaId)
            ->first();



        // Verificar si no existe el registro
        if (!$pvCgshge) {
            return response()->json(['error' => 'No se encontraron materias para los parámetros seleccionados'], 404);
        }

        // Obtener materias relacionadas con el curso
        $materias = Materia::where('cgshe_id', $pvCgshge->id)->get();

        return response()->json($materias);
    }

    public function getMaterias($gestionId, $nivelId, $cursoId, $gradoId, $seccionId, $jornadaId)
    {
        // Validar parámetros
        if (empty($gestionId) || empty($nivelId) || empty($cursoId) || empty($gradoId) || empty($seccionId) || empty($jornadaId)) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        // Buscar el registro en `Cgshge`
        $pvCgshge = Cgshge::where('gestion_id', $gestionId)
            ->where('nivel_id', $nivelId)
            ->where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('seccion_id', $seccionId)
            ->where('jornada_id', $jornadaId)
            ->first();

        // Si no existe el registro, devolver error
        if (!$pvCgshge) {
            return response()->json(['error' => 'No se encontraron materias'], 404);
        }

        // Obtener las materias con la relación `gestionMateria`
        $materias = Materia::where('cgshe_id', $pvCgshge->id)
        ->whereHas('estado', function ($query) {
            $query->where('estado', 'Activo'); // o 'ACTIVO' según tus datos
        })
        ->with(['gestionMateria' => function ($query) {
            $query->select('id', 'nombre', 'descripcion');
        }])
        ->get();



        // Si no hay materias, devolver error
        if ($materias->isEmpty()) {
            return response()->json(['error' => 'No se encontraron materias'], 404);
        }

        return response()->json($materias);
    }






}
