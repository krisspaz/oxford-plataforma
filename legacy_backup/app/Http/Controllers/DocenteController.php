<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdentificacionDocumento;
use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\Privilegio;
use App\Models\Docente;
use App\Models\Seccion;
use App\Models\CMSUser;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $docentes = Docente::all(); // Obtiene todas las personas
        return view('docentes.index', compact('docentes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $privilegios = Privilegio::all();
        $tiposIdentificacion = IdentificacionDocumento::all();

        $estados = Estado::all();
        return view('docentes.create', compact('estados', 'privilegios', 'tiposIdentificacion'));
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


        $docDocente = IdentificacionDocumento::find($request->id_tipoidentificacion_docente);
        $request->validate(
            [
            'fotografia_docente' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nombres_docente' => 'required|string|max:255',
            'apellidos_docente' => 'required|string|max:255',
            'genero_docente' => 'required|string',
            'fecha_nacimiento_docente' => 'required|date',

             'identificacion_docente' => [
                'required',
                'string',
                'max:' . ($docDocente->max_digitos),
                'unique:personas,num_documento',
            ],
            'id_tipoidentificacion_docente' => 'required|exists:tb_identificacion_documentos,id',


            'cedula_docente' => 'nullable|string|max:255',
            'especialidad_docente' => 'nullable|string|max:255',
            'telefono_docente' => 'required|string|max:255',
            'correo_docente' => 'nullable|email|max:255',
            'direccion_docente' => 'required|string|max:255',
            'privilegio_id_docente' => 'required|exists:cms_privileges,id',
        ],
            [

            'correo_docente.unique' => 'El Correo Electrónico del Estudiante ya fue registrado.',
            'correo_docente.email' => 'Debe ingresar un correo electrónico válido.',
            'correo_docente.max' => 'El correo no puede tener más de 255 caracteres.',
            'identificacion_docente.max' => 'El Tipo de Documento de Identificacion requiere '.$docDocente->max_digitos.' digitos',
        ]
        );

        DB::beginTransaction();

        try {

            if ($request->hasFile('fotografia_docente')) {
                $file = $request->file('fotografia_docente');
                if ($file->isValid()) {
                    $path = $file->store('fotografias', 'public');
                } else {
                    return back()->with('error', 'El archivo cargado no es válido.');
                }
            } else {
                $path = null;
            }


            $estadoActivo = Estado::whereRaw('LOWER(estado) = ?', ['activo'])->first();
            $estadoId = $estadoActivo->id;

            $usuarioDocenteId = $this->crearUsuarios(
                $request->nombres_docente,
                $request->correo_docente,
                $request->privilegio_id_docente,
                $path ?? null
            );

            if (is_array($usuarioDocenteId) && isset($usuarioDocenteId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $usuarioDocenteId['error']);
            }

            $usuario_docente_id = $usuarioDocenteId;

            //--------------------------------------------------------------------------------

            $personaDocenteId = $this->crearPersonas(
                null,
                $request->defuncion_docente,
                $request->estadocivil_docente,
                $request->profesion_docente,
                $request->nombres_docente,
                $request->apellidos_docente,
                $request->genero_docente,
                $request->id_tipoidentificacion_docente,
                $request->identificacion_docente,
                $request->fecha_nacimiento_docente,
                $request->correo_docente,
                $usuario_docente_id, // ID del usuario creado anteriormente
                $request->telefono_docente,
                $request->direccion_docente,
            );

            if (is_array($personaDocenteId) && isset($personaDocenteId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $personaDocenteId['error']);
            }

            // Obtener el ID recién generado del estudiante
            $docentepersona_id  = $personaDocenteId ;


            //--------------------------------------------------------------------------


            //Seccion de creacion de alumnos
            $docenteId = $this->crearDocente(
                $path ?? null, // Fotografía
                $docentepersona_id, // ID de la persona (previamente creado)

                $request->especialidad_docente,
                $request->cedula_docente,
                $estadoId
            );

            // Verificar si hubo un error
            if (is_array($docenteId) && isset($docenteId['error'])) {
                // Manejar el error aquí
                return back()->with('error', $docenteId['error']);
            }




            //-------------------------------------------------------------------------

            DB::commit();

            Session::flash('success', '¡Datos guardados correctamente!');
            return redirect()->route('docentes.index')->with('success', 'Docentes creada exitosamente.');


        } catch (\Exception $e) {
            //Si ocurre un error, se revierte la transacción
            DB::rollBack();
            \Log::error('Error al realizar la inscripción: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al realizar la inscripción: ' . $e->getMessage());

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Buscar el docente por su ID
        $docente = Docente::findOrFail($id);

        // Retornar la vista con los datos del docente
        return view('docentes.show', compact('docente'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Obtener la persona asociada al docente
        $persona = Persona::findOrFail($id);

        // Obtener los datos de docenten para completar la edición
        $docente = Docente::where('persona_id', $id)->first();  // Suponiendo que 'persona_id' es la relación en Docente


        $privilegios = Privilegio::all();
        $tiposIdentificacion = IdentificacionDocumento::all();
        $estados = Estado::all();


        // Devolver los datos a la vista
        return view('docentes.edit', compact('persona', 'docente', 'privilegios', 'tiposIdentificacion', 'estados'));
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

        //dd($request->all());
        $docDocente = IdentificacionDocumento::find($request->id_tipoidentificacion_docente);
        $request->validate([
            'nombres_docente' => 'required|string|max:255',
            'apellidos_docente' => 'required|string|max:255',
             'identificacion_docente' => [
                'required',
                'string',
                'max:' . ($docDocente->max_digitos),

            ],
            'id_tipoidentificacion_docente' => 'required|exists:tb_identificacion_documentos,id',
           // 'correo_docente' => 'required|email|unique:personas,email,' . $id,

        ]);

        $docente = Docente::findOrFail($id);

        // Actualizar los campos del docente
        $docente->persona->cmsUser->id_cms_privileges = $request->privilegio_id_docente;


        $docente->persona->nombres = $request->nombres_docente;
        $docente->persona->apellidos = $request->apellidos_docente;
        $docente->persona->genero = $request->genero_docente;
        $docente->persona->fecha_nacimiento = $request->fecha_nacimiento_docente;
        $docente->persona->identificacion_documentos_id = $request->id_tipoidentificacion_docente;
        $docente->persona->num_documento = $request->identificacion_docente;
        $docente->cedula = $request->cedula_docente;
        $docente->especialidad = $request->especialidad_docente;
        $docente->persona->telefono = $request->telefono_docente;
        $docente->persona->email = $request->correo_docente;
        $docente->persona->direccion = $request->direccion_docente;
        $docente->persona->fecha_defuncion = $request->fecha_defuncion_docente;


        // Guardar los cambios en la base de datos
        $docente->push(); // `push()` guarda las relaciones también

        // Redirigir con un mensaje de éxito
        return redirect()->route('docentes.index')->with('success', 'Docente actualizado exitosamente.');
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
        $Docente= Docente::findOrFail($id);
        $Docente->delete();
        return redirect()->route('docentes.index')->with('success', 'Docente eliminada exitosamente.');
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

    public function crearDocente($fotografia, $personaId, $especialidad, $cedula, $estadoId)
    {

        //dd($estadoId);
        // Crear un nuevo registro de estudiante
        $docente = Docente::create([
           'fotografia_docente' => $fotografia,
            'persona_id' => $personaId,
            'especialidad' => $especialidad,

                'cedula' => $cedula,

             'estado_id' => $estadoId
        ]);

        // Verificar si la creación del estudiante falló
        if (!$docente->id) {
            return ['error' => 'Hubo un problema al crear el Docente.'];
        }

        // Retornar el ID del estudiante creado
        return $docente->id;
    }



}
