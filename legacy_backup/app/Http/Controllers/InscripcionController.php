<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parentesco;
use App\Models\IdentificacionDocumento;
use App\Models\Departamento;
use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\Privilegio;
use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\Grado;
use App\Models\Curso;
use App\Models\Estado;

use App\Models\Seccion;
use App\Models\HistorialAcademico;
use App\Models\HistorialMedico;
use App\Models\CMSUser;
use App\Models\Familia;
use App\Models\Cgshge;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InscripcionController extends Controller
{
    public function index()
    {
        $privilegios = Privilegio::all();
        $tiposIdentificacion = IdentificacionDocumento::all();

        return view('inscripcion.index');
    }



    public function create()
    {
        $identificaciones = IdentificacionDocumento::all();
        $parentescos = Parentesco::all();
        $departamentos = Departamento::all();
        $privilegios = Privilegio::all();
        $tiposIdentificacion = IdentificacionDocumento::all();
        $gestiones = Gestion::all();
        $niveles = Nivel::all();
        $grados = Grado::all();
        $ncursos = Curso::all();

        $ngestiones = DB::table('gestionesacademicas')
        ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
        ->where(function ($query) {
            $query->where('tb_estados.estado', 'Activo')
                  ->orWhere('tb_estados.estado', 'ACTIVO');
        })
        ->select('gestionesacademicas.*')
        ->get();

        $cursos = DB::table('pv_cgshges')
            ->join('cursos', 'pv_cgshges.curso_id', '=', 'cursos.id')
            ->distinct()
            ->pluck('cursos.curso');
        $secciones = Seccion::all();


        return view('inscripcion.create', compact('ncursos', 'ngestiones', 'grados', 'secciones', 'gestiones', 'cursos', 'niveles', 'privilegios', 'tiposIdentificacion', 'parentescos', 'departamentos'));
    }

    public function store(Request $request)
    {
        // Validación de datos

        //dd($request->all());

        

        $docEstudiante = IdentificacionDocumento::find($request->id_tipoidentificacion_estudiante);
        $docPadre = IdentificacionDocumento::find($request->id_tipo_padre);
        $docMadre = IdentificacionDocumento::find($request->id_tipo_madre);
        $docEncargado = IdentificacionDocumento::find($request->id_tipo_encargado);
        //  dd( $docEstudiante);

        $request->validate([

            //validacion para Estudiantes
            'fotografia_estudiante' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'nombres_estudiante' => 'required|string|max:255',
            'apellidos_estudiante' => 'required|string|max:255',
            'genero_estudiante' => 'required|string',
            'fecha_nacimiento_estudiante' => 'required|date',

            // Estudiante
            'identificacion_estudiante' => [
                'required',
                'string',
                'max:' . ($docEstudiante->max_digitos),
                'unique:personas,num_documento',
            ],
            'id_tipoidentificacion_estudiante' => 'required|exists:tb_identificacion_documentos,id',

            'carnet_estudiante' => 'required|string|max:255',
            'telefono_estudiante' => 'nullable|string|max:255',

            'correo_estudiante' => 'nullable|email|max:255|unique:personas,email',
            'direccion_estudiante' => 'required|string|max:255',
            'privilegio_id_estudiante' => 'required|exists:cms_privileges,id',

            'grupo_sanguineo' => 'nullable|string|max:5',
            'alergias' => 'nullable|string',
            'enfermedades' => 'nullable|string',
            'medicamentos' => 'nullable|string',
            'medico' => 'nullable|string|max:255',
            'telefono_medico' => 'nullable|string|max:15',
            'observacion' => 'nullable|string',
            'tipo_documento.*' => 'required|string',
            'nombre_documento.*' => 'required|string|max:255',
            'documento.*' => 'required|file|mimes:pdf|max:2048', // Asegúrate de validar el tipo y tamaño del archivo
            'fexpiracion.*' => 'nullable|date',
            'otro_documento.*' => 'nullable|string|max:255', // Validar el campo "otro_documento" si es necesario






             'identificacion_padre' => [
                'nullable',
                'string',
                'max:' . ($docPadre ? $docPadre->max_digitos : 13),
                'unique:personas,num_documento',
            ],
            'id_tipo_padre' => 'nullable|exists:tb_identificacion_documentos,id',
            'profesion_padre' => 'nullable|string|max:255',
            'telefono_padre' => 'nullable|string|max:15',

            'correo_padre' => 'nullable|email|unique:personas,email|max:255',
            'direccion_padre' => 'nullable|string|max:500',


            //validacion madre


             // Madre
            'identificacion_madre' => [
                'nullable',
                'string',
                'max:' . ($docMadre ? $docMadre->max_digitos : 13),
                'unique:personas,num_documento',
            ],
            'id_tipo_madre' => 'nullable|exists:tb_identificacion_documentos,id',
            'profesion_madre' => 'nullable|string|max:255',
            'telefono_madre' => 'nullable|string|max:15',
            'correo_madre' => 'nullable|email|max:255',
            'correo_madre' => 'nullable|email|unique:personas,email|max:255',
            'direccion_madre' => 'nullable|string|max:500',


        ], [
           // Mensajes de error para Estudiante

           'correo_estudiante.unique' => 'El Correo Electrónico del Estudiante ya fue registrado.',
           'correo_estudiante.email' => 'Debe ingresar un correo electrónico válido.',
           'correo_estudiante.max' => 'El correo no puede tener más de 255 caracteres.',
    'fotografia_estudiante.image' => 'La fotografía del estudiante debe ser una imagen.',
    'fotografia_estudiante.mimes' => 'La fotografía del estudiante debe ser de tipo jpeg, png, jpg, gif o svg.',
    'nombres_estudiante.required' => 'El nombre del estudiante es obligatorio.',
    'apellidos_estudiante.required' => 'El apellido del estudiante es obligatorio.',
    'genero_estudiante.required' => 'El género del estudiante es obligatorio.',
    'fecha_nacimiento_estudiante.required' => 'La fecha de nacimiento del estudiante es obligatoria.',
    'id_tipoidentificacion_estudiante.required' => 'El tipo de identificación del estudiante es obligatorio.',
    'id_tipoidentificacion_estudiante.exists' => 'El tipo de identificación seleccionado no es válido.',
    'identificacion_estudiante.required' => 'El número de identificación del estudiante es obligatorio.',
    'identificacion_estudiante.max' => 'El número de identificación del estudiante debe tener exactamente '. ($docEstudiante->max_digitos). ' dígitos.',
    'identificacion_estudiante.unique' => 'El número de identificación del estudiante ya está registrado.',
    'carnet_estudiante.required' => 'El carnet del estudiante es obligatorio.',
    'direccion_estudiante.required' => 'La dirección del estudiante es obligatoria.',

    // Mensajes de error para documentos
    'tipo_documento.*.required' => 'El tipo de documento es obligatorio.',
    'nombre_documento.*.required' => 'El nombre del documento es obligatorio.',
    'documento.*.required' => 'Debe adjuntar un documento.',
    'documento.*.file' => 'El archivo adjunto debe ser un documento válido.',
    'documento.*.mimes' => 'El documento debe estar en formato PDF.',
    'documento.*.max' => 'El documento no debe superar los 2MB.',
    'fexpiracion.*.date' => 'La fecha de expiración debe ser una fecha válida.',

    // Mensajes de error para Padre
    'nombres_padre.required' => 'El nombre del padre es obligatorio.',
    'apellidos_padre.required' => 'El apellido del padre es obligatorio.',
    'genero_padre.required' => 'El género del padre es obligatorio.',
    'fecha_nacimiento_padre.required' => 'La fecha de nacimiento del padre es obligatoria.',
    'id_tipo_padre.required' => 'El tipo de identificación del padre es obligatorio.',
    'identificacion_padre.required' => 'El número de identificación del padre es obligatorio.',
    'identificacion_padre.max' => 'El número de identificación del padre debe tener exactamente '.  ($docEstudiante->max_digitos) .' dígitos.',
    'identificacion_padre.unique' => 'El número de identificación del padre ya está registrado.',


    'correo_padre.unique' => 'El Correo Electrónico del Padre ya fue registrado.',
    'correo_padre.email' => 'Debe ingresar un correo electrónico válido.',
    'correo_padre.max' => 'El correo no puede tener más de 255 caracteres.',
    // Mensajes de error para Madre
    'nombres_madre.required' => 'El nombre de la madre es obligatorio.',
    'apellidos_madre.required' => 'El apellido de la madre es obligatorio.',
    'genero_madre.required' => 'El género de la madre es obligatorio.',
    'fecha_nacimiento_madre.required' => 'La fecha de nacimiento de la madre es obligatoria.',
    'id_tipo_madre.required' => 'El tipo de identificación de la madre es obligatorio.',
    'identificacion_madre.required' => 'El número de identificación de la madre es obligatorio.',
    'identificacion_madre.max' => 'El número de identificación de la madre debe tener exactamente '.  ($docEstudiante->max_digitos) .' dígitos.',
    'identificacion_madre.unique' => 'El número de identificación de la madre ya está registrado.',
       'correo_madre.unique' => 'El Correo Electrónico de la Madre ya fue registrado.',
    'correo_madre.email' => 'Debe ingresar un correo electrónico válido.',
    'correo_madre.max' => 'El correo no puede tener más de 255 caracteres.',

]);




        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Manejo de la fotografía

            $estadoActivo = Estado::whereRaw('LOWER(estado) = ?', ['activo'])->first();
            $estadoId = $estadoActivo->id;

            //------------------------------------------------------------------------------------------------------------------------
            if ($request->hasFile('fotografia_estudiante')) {
                $file = $request->file('fotografia_estudiante');
                $path = $file->store('fotografias_estudiantes', 'public'); // Guarda en storage/app/public/fotografias
            } else {
                $path = null;
            }

            //------------------------------------------------------------------------------------------------------------------------

            //Sesscion de Usuarios


            $usuarioEstudianteId = $this->crearUsuarios(
                $request->nombres_estudiante,
                $request->correo_estudiante,
                $request->privilegio_id_estudiante,
                $path ?? null
            );

            if (is_array($usuarioEstudianteId) && isset($usuarioEstudianted['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $usuarioEstudianteId['error']);
            }

            $usuario_estudiante_id = $usuarioEstudianteId;





            //------------------------------------------------------------------------------------------------------------------------
            //seccion datos personales y creacion de personas

            //________________________________________________________________________

            $personaEstudianteId = $this->crearPersonas(
                null,
                null,
                null,
                null,
                $request->nombres_estudiante,
                $request->apellidos_estudiante,
                $request->genero_estudiante,
                $request->id_tipoidentificacion_estudiante,
                $request->identificacion_estudiante,
                $request->fecha_nacimiento_estudiante,
                $request->correo_estudiante,
                $usuario_estudiante_id,
                $request->telefono_estudiante,
                $request->direccion_estudiante, // ID del usuario creado anteriormente
            );

            if (is_array($personaEstudianteId) && isset($personaEstudianteId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $personaEstudianteId['error']);
            }

            // Obtener el ID recién generado del estudiante
            $persona_estudiante_id = $personaEstudianteId;

            //________________________________________________________________________
            if ($request->has('unico_padre') && $request->input('unico_padre') === '1') {

                //Usuario Padres o Tutor


                $usuarioPadreId = $this->crearUsuarios(
                    $request->nombres_padre,
                    $request->correo_padre,
                    $request->privilegio_id_padre,
                    null
                );

                if (is_array($usuarioPadreId) && isset($usuarioPadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $usuarioPadred['error']);
                }

                $usuario_padre_id = $usuarioPadreId;

                $personaPadreId = $this->crearPersonas(
                    null,
                    $request->defuncion_padre,
                    $request->estadocivil_padre,
                    $request->profesion_padre,
                    $request->nombres_padre,
                    $request->apellidos_padre,
                    $request->genero_padre,
                    $request->id_tipo_padre,
                    $request->identificacion_padre,
                    $request->fecha_nacimiento_padre,
                    $request->correo_padre,
                    $usuario_padre_id, // ID del usuario creado anteriormente
                    $request->telefono_padre,
                    $request->direccion_padre
                );

                if (is_array($personaPadreId) && isset($personaPadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $personaPadreId['error']);
                }

                // Obtener el ID recién generado del estudiante
                $padre_id  = $personaPadreId ;




            }

            //________________________________________________________________________

            elseif ($request->has('unico_madre') && $request->input('unico_madre') === '1') {

                $usuarioMadreId = $this->crearUsuarios(
                    $request->nombres_madre,
                    $request->correo_madre,
                    $request->privilegio_id_padre,
                    null
                );

                if (is_array($usuarioMadreId) && isset($usuarioMadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $usuarioPadred['error']);
                }

                $usuario_padre_id = $usuarioMadreId;


                $personaMadreId = $this->crearPersonas(
                    null,
                    $request->defuncion_madre,
                    $request->estadocivil_madre,
                    $request->profesion_madre,
                    $request->nombres_madre,
                    $request->apellidos_madre,
                    $request->genero_madre,
                    $request->id_tipo_madre,
                    $request->identificacion_madre,
                    $request->fecha_nacimiento_madre,
                    $request->correo_madre,
                    $usuario_padre_id, // ID del usuario creado anteriormente
                    $request->telefono_madre,
                    $request->direccion_madre
                );

                if (is_array($personaMadreId) && isset($personaMadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $personaMadreId['error']);
                }

                // Obtener el ID recién generado del estudiante
                $madre_id =  $personaMadreId ;


                //Usuario Padres o Tutor








            } else {

                //Usuario Padres o Tutor


                $usuarioPadreId = $this->crearUsuarios(
                    $request->nombres_padre,
                    $request->correo_padre,
                    $request->privilegio_id_padre,
                    null
                );

                if (is_array($usuarioPadreId) && isset($usuarioPadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $usuarioPadred['error']);
                }

                $usuario_padre_id = $usuarioPadreId;

                $personaPadreId = $this->crearPersonas(
                    null,
                    $request->defuncion_padre,
                    $request->estadocivil_padre,
                    $request->profesion_padre,
                    $request->nombres_padre,
                    $request->apellidos_padre,
                    $request->genero_padre,
                    $request->id_tipo_padre,
                    $request->identificacion_padre,
                    $request->fecha_nacimiento_padre,
                    $request->correo_padre,
                    $usuario_padre_id, // ID del usuario creado anteriormente
                    $request->telefono_padre,
                    $request->direccion_padre
                );

                if (is_array($personaPadreId) && isset($personaPadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $personaPadreId['error']);
                }

                // Obtener el ID recién generado del estudiante
                $padre_id  = $personaPadreId ;


                $personaMadreId = $this->crearPersonas(
                    null,
                    $request->defuncion_madre,
                    $request->estadocivil_madre,
                    $request->profesion_madre,
                    $request->nombres_madre,
                    $request->apellidos_madre,
                    $request->genero_madre,
                    $request->id_tipo_madre,
                    $request->identificacion_madre,
                    $request->fecha_nacimiento_madre,
                    $request->correo_madre,
                    $usuario_padre_id, // ID del usuario creado anteriormente
                    $request->telefono_madre,
                    $request->direccion_madre
                );

                if (is_array($personaMadreId) && isset($personaMadreId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $personaMadreId['error']);
                }

                // Obtener el ID recién generado del estudiante
                $madre_id =  $personaMadreId ;


            }




            //________________________________________________________________________

            //------------------------------------------------------------------------------------------------------------------------
            //Seccion asignacion de cursos

            $asignacionid = null;

            //------------------------------------------------------------------------------------------------------------------------

            //Seccion de creacion de alumnos
            $estudianteId = $this->crearEstudiante(
                $path ?? null, // Fotografía
                $persona_estudiante_id, // ID de la persona (previamente creado)
                $request->carnet_estudiante, // Carnet del estudiante
                $asignacionid, // ID tabla pivote donde esta gestiones,cursos,grados,secciones y jornadas
                $estadoId
            );

            // Verificar si hubo un error
            if (is_array($estudianteId) && isset($estudianteId['error'])) {
                // Manejar el error aquí
                return back()->with('error', $estudianteId['error']);
            }

            //------------------------------------------------------------------------------------------------------------------------
            //Seccion Hisotrial Academico
            /*
                               try {
                                  // $this->crearHistorialAcademico($request->historialAcademico, $estudianteId,$estadoId);
                               } catch (\Exception $e) {
                                   // Manejar el error, por ejemplo:
                                   return back()->with('error', $e->getMessage());
                               }  */
            //------------------------------------------------------------------------------------------------------------------------
            //Seccion Hisotrial Medico
            try {
                $this->crearHistorialMedico($request, $estudianteId, $estadoId);
            } catch (\Exception $e) {
                // Manejar el error, por ejemplo:
                return back()->with('error', $e->getMessage());
            }
            //------------------------------------------------------------------------------------------------------------------------
            //Documentos de Inscripción

            $this->guardarDocumentosInscripcion($request, $estudianteId, $estadoId);


            //------------------------------------------------------------------------------------------------------------------------
            //Seccion Familiar



            if ($request->has('datos_padre') && $request->input('datos_padre') === '1') {
                // Lógica cuando el checkbox está marcado
                $this->crearfamilia(
                    $nombrefamiliar = $request->apellidos_estudiante,
                    $padre_id,
                    $madre_id,
                    $padre_id,
                    $estudianteId,
                    $estadoId
                );

            } elseif ($request->has('datos_madre') && $request->input('datos_madre') === '1') {

                $this->crearfamilia(
                    $nombrefamiliar = $request->apellidos_estudiante,
                    $padre_id,
                    $madre_id,
                    $madre_id,
                    $estudianteId,
                    $estadoId
                );
            } elseif ($request->has('datos_madre') && $request->input('datos_madre') === '1' && $request->has('unico_madre') && $request->input('unico_madre') === '1') {

                $this->crearfamilia(
                    $nombrefamiliar = $request->apellidos_estudiante,
                    null,
                    $madre_id,
                    $madre_id,
                    $estudianteId,
                    $estadoId
                );

            } elseif ($request->has('datos_padre') && $request->input('datos_padre') === '1' && $request->has('unico_padre') && $request->input('unico_padre') === '1') {

                $this->crearfamilia(
                    $nombrefamiliar = $request->apellidos_estudiante,
                    $padre_id,
                    $madre_id,
                    $madre_id,
                    $estudianteId,
                    $estadoId
                );

            } else {
                $personaEncargadoId = $this->crearPersonas(
                    null,
                    $request->defuncion_encargado,
                    $request->estadocivil_encargado,
                    $request->profesion_encargado,
                    $request->nombres_encargado,
                    $request->apellidos_encargado,
                    $request->genero_encargado,
                    $request->id_tipo_encargado,
                    $request->identificacion_encargado,
                    $request->fecha_nacimiento_encargado,
                    $request->correo_encargado,
                    $usuario_padre_id,// ID del usuario creado anteriormente
                    // ID del usuario creado anteriormente
                    $request->telefono_encargado,
                    $request->direccion_encargado
                );

                if (is_array($personaEncargadoId) && isset($personaEncargadoId['error'])) {
                    // Maneja el error aquí si ocurrió uno
                    return back()->with('error', $personaEncargadoId['error']);
                }

                // Obtener el ID recién generado del estudiante
                $encargado_id =  $personaEncargadoId ;

                $this->crearfamilia(
                    $nombrefamiliar = $request->apellidos_estudiante,
                    $padre_id,
                    $madre_id,
                    $encargado_id,
                    $estudianteId,
                    $estadoId
                );



            }






            //------------------------------------------------------------------------------------------------------------------------


            DB::commit();

            // Redirige a la página de inscripciones con un mensaje de éxito
            Session::flash('success', '¡Datos guardados correctamente!');
            return redirect()->route('inscripcion.index');

        } catch (\Exception $e) {
            //Si ocurre un error, se revierte la transacción
            DB::rollBack();
            \Log::error('Error al realizar la inscripción: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al realizar la inscripción: ' . $e->getMessage());

        }
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



    public function crearUsuarios($nombres, $correo, $privilegio_id, $ruta_foto = null)
    {
        try {
            // Crear la contraseña por defecto
            $password = Hash::make('12345678');

            // Crear el usuario
            $usuario = CMSUser::create([
                'name' => $nombres,
                'photo' => $ruta_foto,
                'email' => $correo,
                'password' => $password,
                'id_cms_privileges' => $privilegio_id,
            ]);

            // Verificar si se creó correctamente
            if (!$usuario->id) {
                throw new \Exception('Hubo un problema al crear el usuario del estudiante.');
            }

            // Retornar el ID del usuario creado si es necesario
            return $usuario->id;

        } catch (\Exception $e) {
            // Manejo de excepciones
            return ['error' => $e->getMessage()];
        }
    }


    public function crearPersonas($parentesco, $fecha_defuncion, $estado_civil, $prfoesion, $nombres, $apellidos, $genero, $tipoIdentificacionId, $numDocumento, $fechaNacimiento, $correo, $usuarioId, $telefono, $direccion)
    {

        // Crear el registro de persona para el estudiante
        $persona = Persona::create([
            'parentesco_id' => null, // El valor fijo
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'genero' => $genero,
            'estado_civil' => $estado_civil,
            'apellido_casada' => null, // Campo fijo
            'identificacion_documentos_id' => $tipoIdentificacionId,
            'num_documento' => $numDocumento,
            'fecha_nacimiento' => $fechaNacimiento,
            'profesion' => $prfoesion,
            'email' => $correo,
            'fecha_defuncion' => $fecha_defuncion,
            'cms_users_id' => $usuarioId,
            'telefono' => $telefono,
            'direccion' => $direccion,

        ]);

        // Verificar si se creó correctamente
        if (!$persona->id) {
            throw new \Exception('Hubo un problema al crear el registro del estudiante.');
        }

        // Retornar el ID de la persona creada si es necesario
        //dd( $persona->id );
        return $persona->id;

    }

    public function crearfamilia($nombrefamiliar, $padreId, $madreId, $encargadoId, $estudianteId, $estadoId)
    {
        // Obtener el año actual
        $año = date('Y');

        // Calcular el correlativo (puedes ajustar esto según tus necesidades, aquí simplemente cuenta el número total de registros)
        $correlativo = str_pad(Familia::count() + 1, 5, '0', STR_PAD_LEFT);

        // Crear un placeholder temporal para el ID, que será reemplazado después de crear el registro
        $codigoFamiliarTemporal = "F-{$año}-XXXX{$correlativo}";

        // Crear la familia (sin el código final aún)
        $familia = Familia::create([
            'nombre_familiar' => $nombrefamiliar,
            'codigo_familiar' => $codigoFamiliarTemporal, // Temporal
            'padre_persona_id' => $padreId,
            'madre_persona_id' => $madreId,
            'encargado_persona_id' => $encargadoId,
            'estudiante_id' => $estudianteId,
            'estado_id' => $estadoId,
        ]);

        // Verificar si la creación de la familia falló
        if (!$familia->id) {
            return ['error' => 'Hubo un problema al crear la familia.'];
        }

        // Generar el código familiar con el ID real
        $codigoFamiliar = "F-{$año}-{$familia->id}{$correlativo}";

        // Actualizar el registro con el código correcto
        $familia->codigo_familiar = $codigoFamiliar;
        $familia->save();

        // Retornar el ID del registro creado
        return $familia->id;
    }


    public function crearEstudiante($fotografia, $personaId, $carnet, $cursoId, $estadoId)
    {
        // Crear un nuevo registro de estudiante


        $EstudianteRegistrado = Estudiante::where('persona_id', $personaId)->first();


        if ($EstudianteRegistrado) {





            return  $EstudianteRegistrado->id;

        } else {




            $estudiante = Estudiante::create([
               'fotografia_estudiante' => $fotografia,
                'persona_id' => $personaId,
                'carnet' => $carnet,
                'cgshges_id' => $cursoId,
                 'estado_id' => $estadoId
            ]);

            // Verificar si la creación del estudiante falló
            if (!$estudiante->id) {
                return ['error' => 'Hubo un problema al crear el estudiante.'];
            }

            // Retornar el ID del estudiante creado
            return $estudiante->id;

        }

    }


    public function crearHistorialAcademico($historialAcademicoJson, $estudianteId, $estadoId)
    {
        // Decodificar el JSON a un arreglo
        $historialAcademico = json_decode($historialAcademicoJson, true);

        // Verificar si hubo un error de formato JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('El formato de los datos del historial académico no es válido.');
        }

        // Crear los registros del historial académico
        foreach ($historialAcademico as $item) {
            HistorialAcademico::create([
                'estudiante_id' => $estudianteId,
                'nivel_id' => $item['nivel'],
                'curso_id' => $item['curso'],
                'grado_id' => $item['grado'],
                'año' => $item['anio'],
                'establecimiento' => $item['establecimiento'],
                'estado_id' => $estadoId,

            ]);
        }
    }

    public function crearHistorialMedico($request, $estudianteId, $estadoId)
    {



        $EstudianteHistorialMedico = HistorialMedico::where('estudiante_id', $estudianteId)->first();

        if ($EstudianteHistorialMedico) {


            return $EstudianteHistorialMedico->id;

        } else {
            return HistorialMedico::create([
                'estudiante_id' => $estudianteId,
                'grupo_sanguineo' => $request->input('grupo_sanguineo')?: null,
                'alergias' => $request->input('alergias'),
                'enfermedades' => $request->input('enfermedades'),
                'medicamentos' => $request->input('medicamentos'),
                'medico' => $request->input('medico'),
                'telefono_medico' => $request->input('telefono_medico'),
                'observacion' => $request->input('observacion'),
                'estado_id' => $estadoId,
            ]);

        }
    }


    public function guardarDocumentosInscripcion($request, $estudianteId, $estadoId)
    {
        if ($request->has('tipo_documento')) {
            foreach ($request->tipo_documento as $index => $tipoDocumento) {
                // Determinar el tipo de documento final
                $tipoDocumentoFinal = $tipoDocumento === 'Otro' ? $request->otro_documento[$index] ?? 'Otro' : $tipoDocumento;

                // Manejo de la subida del archivo
                $nombreDocumento = $request->file('documento')[$index]->getClientOriginalName();
                $rutaDocumento = $request->file('documento')[$index]->storeAs('documentos', $nombreDocumento, 'public');

                // Insertar el registro en la tabla documentosinscripciones
                \DB::table('documentosinscripciones')->insert([
                    'estudiante_id' => $estudianteId, // Asegurarse de que el ID sea proporcionado
                    'tipo_documento' => $tipoDocumentoFinal, // Valor final del tipo de documento
                    'nombre_documento' => $request->nombre_documento[$index],
                    'documento' => $rutaDocumento, // Ruta del documento almacenado
                    'fexpiracion' => $request->fexpiracion[$index] ?? null,
                    'estado_id'=> $estadoId,
                ]);
            }
        }
    }



}
