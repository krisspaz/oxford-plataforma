<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Cgshge;
use App\Models\Grado;
use App\Models\Curso;
use App\Models\Nivel;
use App\Models\Seccion;
use App\Models\Jornada;
use App\Models\Gestion;
use App\Models\Matriculacion;

class ReporteAcademicoController extends Controller
{

    public function filtrarEstudiantes()
    {
        // Obtener los ciclos escolares únicos
        $ciclos = Matriculacion::distinct()->pluck('ciclo_escolar');
        $grados = Grado::all();
        $cursos = Curso::all();
        $niveles = Nivel::all();
        $secciones = Seccion::all();
        $jornadas = Jornada::all();


      
        return view('reportes_academicos.filtrar_estudiantes', [
            'ciclos' => $ciclos,
            'grados' => $grados,
            'cursos' => $cursos,
            'secciones' => $secciones,
            'jornadas' => $jornadas,
            'niveles' => $niveles,
        ]);

        //return view('reportes_academicos.filtrar_estudiantes', compact('ciclos'));
    }

    public function generarPDF(Request $request)
    {

        $cicloEscolar = $request->input('ciclo_escolar');
        $thoja = $request->input('tamano_hoja');
        $ohoja = $request->input('orientacion');
    
        // Obtener el ID de la gestión
        $gestionId = Gestion::where('ciclo_escolar', $cicloEscolar)->value('id');
    
        // Obtener el ID de la asignación completa (nivel, curso, grado, sección, jornada)
        $asignacionid = $this->getPvCgshgeId(
            $gestionId,
            $request->input('nivel'),
            $request->input('curso'),
            $request->input('grado'),
            $request->input('seccion'),
            $request->input('jornada')
        );
    
        // Obtener estudiantes únicamente por asignación y ciclo escolar
        $estudiantes = Matriculacion::with(['estudiante', 'cgshges', 'paquete', 'estado'])
            ->where('ciclo_escolar', $cicloEscolar)
            ->when($asignacionid, function ($query) use ($asignacionid) {
                return $query->where('cgshges_id', $asignacionid);
            })
            ->get();
    
        // Generar vista para el PDF
        $pdf = \PDF::loadView('reportes_academicos.pdf_estudiantes', compact('estudiantes', 'cicloEscolar'))
            ->setPaper($thoja, $ohoja);
    
        return $pdf->download("Listado_Estudiantes_$cicloEscolar.pdf");
    }
  
    public function index()
    {
        //
    }

   
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
}
