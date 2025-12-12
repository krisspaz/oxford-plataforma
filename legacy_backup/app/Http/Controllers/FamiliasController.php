<?php

namespace App\Http\Controllers;

use App\Models\Familia; // Asegúrate de tener el modelo correspondiente
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
use App\Models\Seccion;
use App\Models\HistorialAcademico;
use App\Models\HistorialMedico;
use App\Models\CMSUser;
use App\Models\Cgshge;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class FamiliasController extends Controller
{
    public function index()
    {
        // Obtener todas las familias con las relaciones `estudiante` y `persona`

        $familias = Familia::with(['padre', 'madre', 'estudiantes2.persona'])
            ->get()
            ->groupBy('codigo_familiar');

        // Retornar la vista con los datos de las familias
        return view('familias.index', compact('familias'));
    }
    public function create2($id)
    {
        // Buscar la familia por ID
        $familia = Familia::with(['padre', 'madre', 'estudiantes'])->findOrFail($id);
        $identificaciones = IdentificacionDocumento::all();
        $privilegios = Privilegio::all();


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


        return view('familias.create', compact('familia', 'ncursos', 'ngestiones', 'grados', 'secciones', 'gestiones', 'cursos', 'niveles', 'privilegios', 'tiposIdentificacion', 'parentescos', 'departamentos'));

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


        return view('familias.create', compact('ncursos', 'ngestiones', 'grados', 'secciones', 'gestiones', 'cursos', 'niveles', 'privilegios', 'tiposIdentificacion', 'parentescos', 'departamentos'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        // Validación de datos

        $docEstudiante = IdentificacionDocumento::find($request->id_tipoidentificacion_estudiante);
        $request->validate(
            [

            //validacion para Estudiantes
            'fotografia_estudiante' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'nombres_estudiante' => 'required|string|max:255',
            'apellidos_estudiante' => 'required|string|max:255',
            'genero_estudiante' => 'required|string',
            'fecha_nacimiento_estudiante' => 'required|date',

            // Estudiante
            'identificacion_estudiante' => [
                'required',
                'integer',
                'digits:' . ($docEstudiante->max_digitos),
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

            //Validacion Padre
           // 'privilegio_id_padre' => 'required|exists:cms_privileges,id',
            'id_padre' => 'nullable|string|max:255',



            //validacion madre
            // 'privilegio_id_madre' => 'required|exists:cms_privileges,id',
            'id_madre' => 'nullable|string|max:255',


            //validacion encargado
            // 'privilegio_id_encargado' => 'required|exists:cms_privileges,id',
            'id_encargado' => 'nullable|string|max:255',

            'codigo' => 'nullable|string|max:255',





        ],
            [

            'correo_estudiante.unique' => 'El Correo Electrónico del Estudiante ya fue registrado.',
            'correo_estudiante.email' => 'Debe ingresar un correo electrónico válido.',
            'correo_estudiante.max' => 'El correo no puede tener más de 255 caracteres.',
             'identificacion_estudiante.required' => 'El número de identificación del estudiante es obligatorio.',
    'identificacion_estudiante.digits' => 'El número de identificación del estudiante debe tener exactamente '. ($docEstudiante->max_digitos). ' dígitos.',
    'identificacion_estudiante.unique' => 'El número de identificación del estudiante ya está registrado.',
        ]
        );

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Manejo de la fotografía

            $estadoId = "1";

            if ($request->hasFile('fotografia_estudiante')) {
                $file = $request->file('fotografia_estudiante');
                $path = $file->store('fotografias_estudiantes', 'public'); // Guarda en storage/app/public/fotografias
            } else {
                $path = null;
            }




            $usuarioId = $this->crearUsuarios(
                $request->nombres_estudiante,
                $request->correo_estudiante,
                $request->privilegio_id_estudiante,
                $path ?? null
            );

            if (is_array($usuarioId) && isset($usuarioId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $usuarioId['error']);
            }

            $usuario_estudiante_id = $usuarioId;

            // Continúa con tu lógica si la creación del usuario fue exitosa




            // Crear registro de la persona (estudiante)


            $parentesco_estudiante = "Estudiante";

            //funcion persona

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
                $request->direccion_estudiante,
            );

            if (is_array($personaEstudianteId) && isset($personaEstudianteId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $personaEstudianteId['error']);
            }

            // Obtener el ID recién generado del estudiante
            $persona_estudiante_id = $personaEstudianteId;



            // Llamar a la función que obtiene el ID de pv_cgshges



            // Se establece como estudiante
            // Llamar a la función para crear el estudiante
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

            // Verificación de formato JSON válido para historial académico




            // Registra información médica
            try {
                $this->crearHistorialMedico($request, $estudianteId, $estadoId);
            } catch (\Exception $e) {
                // Manejar el error, por ejemplo:
                return back()->with('error', $e->getMessage());
            }



            // Procesar cada conjunto de datos de documentos
            $this->guardarDocumentosInscripcion($request, $estudianteId, $estadoId);


            /* */


            // Crear registro de la persona (Padre)


            // Obtener el ID recién generado del estudiante
            $padre_id  = $request->id_padre;

            // Obtener el ID recién generado del estudiante
            $madre_id =  $request->id_madre;


            //usuario Encargado







            // Obtener el ID recién generado del estudiante
            $encargado_id =  $request->id_encargado;
            $codigo = $request->codigo;

            $this->crearfamilia(
                $nombrefamiliar = $request->nombre_familiar,
                $codigo,
                $padre_id,
                $madre_id,
                $encargado_id,
                $estudianteId,
                $estadoId
            );











            // Si todo está bien, se confirma la transacción
            DB::commit();

            // Redirige a la página de inscripciones con un mensaje de éxito
            Session::flash('success', '¡Datos guardados correctamente!');
            return redirect()->route('familias.index');

        } catch (\Exception $e) {
            // Si ocurre un error, se revierte la transacción
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
        $gradoId = $request->input('grado_id');

        if (!$gradoId) {
            return response()->json(['error' => 'El grado_id es requerido.'], 400);
        }

        $secciones = DB::table('pv_cgshges')
        ->join('secciones', 'pv_cgshges.seccion_id', '=', 'secciones.id')
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
        $cursoId = $request->input('curso_id');

        if (!$cursoId) {
            return response()->json(['error' => 'El curso_id es requerido.'], 400);
        }

        $jornadas = DB::table('pv_cgshges')
        ->join('cursos', 'pv_cgshges.curso_id', '=', 'cursos.id')
        ->join('pv_jornada_dia_horarios', 'pv_cgshges.jornada_id', '=', 'pv_jornada_dia_horarios.id')
        ->join('tb_jornadas', 'pv_jornada_dia_horarios.jornada_id', '=', 'tb_jornadas.id')
        ->where('pv_cgshges.curso_id', $cursoId)
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
        try {
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
            return $persona->id;

        } catch (\Exception $e) {
            // Manejo de excepciones
            return ['error' => $e->getMessage()];
        }
    }

    public function crearfamilia($nombrefamiliar, $codigoF, $padreId, $madreId, $encargadoId, $estudianteId, $estadoId)
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
            'codigo_familiar' => $codigoF, // Temporal
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
        $codigoFamiliar = $codigoF;

        // Actualizar el registro con el código correcto
        $familia->codigo_familiar = $codigoFamiliar;
        $familia->save();

        // Retornar el ID del registro creado
        return $familia->id;
    }


    public function crearEstudiante($fotografia, $personaId, $carnet, $cursoId, $estadoId)
    {
        // Crear un nuevo registro de estudiante
        $estudiante = Estudiante::create([
            'fotografia_estudiante' => $fotografia,
            'persona_id' => $personaId,
            'carnet' => $carnet,
            'cgshges_id' => $cursoId,
            'estado_id' => $estadoId,

        ]);

        // Verificar si la creación del estudiante falló
        if (!$estudiante->id) {
            return ['error' => 'Hubo un problema al crear el estudiante.'];
        }

        // Retornar el ID del estudiante creado
        return $estudiante->id;
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
                'estado_id' =>$estadoId
            ]);
        }
    }

    public function crearHistorialMedico($request, $estudianteId, $estadoId)
    {


        return HistorialMedico::create([
            'estudiante_id' => $estudianteId,
            'grupo_sanguineo' => $request->grupo_sanguineo,
            'alergias' => $request->input('alergias'),
            'enfermedades' => $request->input('enfermedades'),
            'medicamentos' => $request->input('medicamentos'),
            'medico' => $request->input('medico'),
            'telefono_medico' => $request->input('telefono_medico'),
            'observacion' => $request->input('observacion'),
            'estado_id' =>$estadoId
        ]);
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
                    'estado_id' => $estadoId,

                ]);
            }
        }
    }







    public function show(Familia $familiar)
    {


        // Buscar la familia por Codigo Familiar
        $familias = Familia::with(['padre', 'madre', 'estudiantes2.persona', 'estudiantes2.medicos','estudiantes2.academicos'])
    ->where('codigo_familiar', $familiar->codigo_familiar)
    ->get()
    ->groupBy('codigo_familiar');

        // dd($familias);
        $identificaciones = IdentificacionDocumento::all();
        $privilegios = Privilegio::all();

        $niveles = Nivel::all();
        $grados =Grado::all();
        $ncursos = Curso::all();






        // Pasar los datos a la vista 'show' para mostrar los detalles de la familia
        return view('familias.show', compact('familias', 'niveles', 'grados', 'ncursos', 'privilegios', 'identificaciones'));

    }

    public function update(Request $request, $id)
    {



        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'genero' => 'required|string|max:255',
            'profesion' => 'nullable|string|max:255',
            'estadocivil' => 'required|string|max:255',
            'id_tipo' => 'required|string|max:255',
            'identificacion' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|string|max:255',

            'defuncion' => 'nullable|string|max:255',
            'correo' => 'nullable|string|max:255',

            'telefono' => 'nullable|string|max:20',
            'direccion' => 'required|string|max:500',
        ]);

        $persona = Persona::findOrFail($id);
        $persona->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'genero' => $request->genero,
            'profesion' => $request->profesion,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'estado_civil'=> $request->estadocivil,



            'identificacion_documentos_id' => $request->id_tipo,
            'num_documento' => $request->identificacion,
            'fecha_nacimiento'=> $request->fecha_nacimiento,
            'email'=> $request->correo,
            'fecha_defuncion'=>$request->defuncion,

        ]);

        // $familia = Familia::findOrFail($request->familia_id);
















        return redirect()->route('familias.show', $request->familia_id)
                         ->with('success', 'Información actualizada correctamente.');
    }


    public function editarAcademico(Request $request)
    {
        try {


            // dd($request->all());
            $estadoId = "1";

            // Eliminar el historial académico previo del estudiante
            HistorialAcademico::where('estudiante_id', $request->idPersonaacademico)->delete();

            // Decodificar el JSON a un arreglo
            $historialAcademico = json_decode($request->historialAcademico, true);

            // Verificar si hubo un error de formato JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('El formato de los datos del historial académico no es válido.');
            }

            // Crear un solo registro con todos los datos en el campo historial_data
            HistorialAcademico::create([
                'estudiante_id' => $request->idPersonaacademico,
                'historial_data' => $historialAcademico, // Guardamos todo el array en el campo JSON
                'estado_id' => $estadoId,
            ]);

            return redirect()->route('familias.show', $request->familia_id)
                ->with('success', 'Información actualizada correctamente.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }




    public function editarHistorialMedico(Request $request)
    {


        //dd($request->all());
        $HistorialMedico = HistorialMedico::findOrFail($request->estudianteidmedico);

        // dd($HistorialMedico);


        $HistorialMedico->update([

             'grupo_sanguineo'=> $request->grupo_sanguineo,
             'alergias' => $request->alergias,
             'enfermedades' => $request->enfermedades,
             'medicamentos' => $request->Medicamentos,
             'medico' => $request->medico,
             'telefono_medico' => $request->telefono_medico,
             'observacion' => $request->observaciones,
         ]);



        return redirect()->route('familias.show', $request->familia_id)
                        ->with('success', 'Información actualizada correctamente.');

    }

    public function editarEstudiante(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'fotografias_estudiante' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'genero' => 'required|string|max:255',

            'id_tipo' => 'required|string|max:255',
            'identificacion' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|string|max:255',

            'defuncion' => 'nullable|string|max:255',
            'carnet' => 'nullable|string|max:255',
            'correo' => 'nullable|string|max:255',

            'telefono' => 'nullable|string|max:20',
            'direccion' => 'required|string|max:500',


            'grupo_sanguineo'  => 'nullable|string|max:5',
            'alergias' => 'nullable|string|max:500',
            'enfermedades' => 'nullable|string|max:500',
            'Medicamentos' => 'nullable|string|max:500',
            'medico' => 'nullable|string|max:500',
            'telefono_medico' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:500',


        ]);



        $persona = Persona::findOrFail($request->idPersonaestudiante);

        $estudiante = Estudiante::findOrFail($request->estudianteidestudiante);

        $path = $estudiante->fotografia_estudiante; // Si no se sube una nueva foto, se mantiene la anterior

        // Si hay una nueva fotografía, eliminar la anterior y guardarla
        if ($request->hasFile('fotografia_estudiante')) { // Cambiar 'fotografias_estudiante' a 'fotografia_estudiante'
            // Eliminar la foto anterior si existe
            if ($estudiante->fotografia_estudiante) {
                Storage::disk('public')->delete($estudiante->fotografia_estudiante);
            }

            // Guardar la nueva fotografía
            $file = $request->file('fotografia_estudiante');
            $path = $file->store('fotografias_estudiantes', 'public'); // Guarda en storage/app/public/fotografias
        }

        // Actualizar el resto de los campos
        $estudiante->update([
            'fotografia_estudiante' => $path, // Actualizar el campo fotografía con la nueva ruta o la anterior
            'carnet' => $request->carnet, // Otros campos a actualizar
        ]);


        $persona->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'genero' => $request->genero,

            'telefono' => $request->telefono,
            'direccion' => $request->direccion,


            'identificacion_documentos_id' => $request->id_tipo,
            'num_documento' => $request->identificacion,
            'fecha_nacimiento'=> $request->fecha_nacimiento,
            'email'=> $request->correo,
            'fecha_defuncion'=>$request->defuncion,

        ]);



        return redirect()->route('familias.show', $request->familia_id)
                         ->with('success', 'Información actualizada correctamente.');
    }

    public function getFormPersona(Request $request)
    {
        $prefijo = $request->get('prefijo');
        return view('familias.forms.forms_persona', compact('prefijo'));
    }

    public function edit($id)
    {
        $persona = Persona::findOrFail($id); // Cambia "Persona" por el modelo que corresponda
        $prefijo = request()->input('prefijo'); // El prefijo se pasa desde el botón o script
        return view('familias.forms.forms_persona', compact('persona', 'prefijo'));
    }


    //registrar



}
