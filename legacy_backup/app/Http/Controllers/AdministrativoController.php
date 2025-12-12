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
use App\Models\Cargo;
use App\Models\Administrativo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AdministrativoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $administrativos = Administrativo::all(); // Obtiene todas las personas
        return view('administrativos.index', compact('administrativos'));
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
        $cargos = Cargo::all();
        $tiposIdentificacion = IdentificacionDocumento::all();

        $estados = Estado::all();
        return view('administrativos.create', compact('estados', 'privilegios', 'tiposIdentificacion', 'cargos'));
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

        $docAdministrativo = IdentificacionDocumento::find($request->id_tipoidentificacion_administrativo);
        //dd($request->all());
        $request->validate([
            'fotografia_administrativo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3000',
            'nombres_administrativo' => 'required|string|max:255',
            'apellidos_administrativo' => 'required|string|max:255',
            'genero_administrativo' => 'required|string',
            'fecha_nacimiento_administrativo' => 'required|date',


             // Identificacion
            'identificacion_administrativo' => [
                'required',
                'string',
                'max:' . ($docAdministrativo->max_digitos),
                'unique:personas,num_documento',
            ],
            'id_tipoidentificacion_administrativo' => 'required|exists:tb_identificacion_documentos,id',


            'cargo_id' => 'required|exists:cargos,id',

            'telefono_administrativo' => 'required|string|max:255',
            'correo_administrativo' => 'nullable|email|max:255',
            'direccion_administrativo' => 'required|string|max:255',
            'privilegio_id_administrativo' => 'required|exists:cms_privileges,id',
             'identificacion_administrativo.required' => 'El número de identificación es obligatorio.',
    'identificacion_administrativo.max' => 'El número de identificación debe tener exactamente '. ($docAdministrativo->max_digitos). ' dígitos.',
    'identificacion_administrativo.unique' => 'El número de identificación ya está registrado.',
        ]);

        DB::beginTransaction();

        try {

            if ($request->hasFile('fotografia_administrativo')) {
                $file = $request->file('fotografia_administrativo');
                if ($file->isValid()) {
                    $path = $file->store('fotografias', 'public');
                } else {
                    return back()->with('error', 'El archivo cargado no es válido.');
                }
            } else {
                $path = null;
            }

            $estadoId = "1";

            $usuarioAdministrativoId = $this->crearUsuarios(
                $request->nombres_administrativo." ". $request->apellidos_administrativo,
                $request->correo_administrativo,
                $request->privilegio_id_administrativo,
                $path ?? null
            );

            if (is_array($usuarioDocenteId) && isset($usuarioAdministrativoId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $usuarioAdministrativoId['error']);
            }

            $usuario_administrativo_id = $usuarioAdministrativoId;

            //--------------------------------------------------------------------------------

            $personaAdministrativoId = $this->crearPersonas(
                null,
                $request->defuncion_administrativo,
                $request->estadocivil_administrativo,
                $request->profesion_administrativo,
                $request->nombres_administrativo,
                $request->apellidos_administrativo,
                $request->genero_administrativo,
                $request->id_tipoidentificacion_administrativo,
                $request->identificacion_administrativo,
                $request->fecha_nacimiento_administrativo,
                $request->correo_administrativo,
                $usuario_administrativo_id, // ID del usuario creado anteriormente
                $request->telefono_administrativo,
                $request->direccion_administrativo,
            );

            if (is_array($personaAdministrativoId) && isset($personaAdministrativoId['error'])) {
                // Maneja el error aquí si ocurrió uno
                return back()->with('error', $personaAdministrativoId['error']);
            }

            // Obtener el ID recién generado del estudiante
            $administrativopersona_id  = $personaAdministrativoId ;


            //--------------------------------------------------------------------------


            //Seccion de creacion de alumnos
            $administrativoId = $this->crearAdministrativo(
                $path ?? null, // Fotografía
                $administrativopersona_id, // ID de la persona (previamente creado)

                $request->cargo_id,
                $estadoId
            );

            // Verificar si hubo un error
            if (is_array($administrativoId) && isset($administrativoId['error'])) {
                // Manejar el error aquí
                return back()->with('error', $administrativoId['error']);
            }




            //-------------------------------------------------------------------------

            DB::commit();

            Session::flash('success', '¡Datos guardados correctamente!');
            return redirect()->route('administrativos.index')->with('success', 'Docentes creada exitosamente.');


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
        $administrativo = Administrativo::findOrFail($id);

        // Retornar la vista con los datos del docente
        return view('administrativos.show', compact('administrativo'));
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
        $cargos = Cargo::all();

        // Obtener los datos de docenten para completar la edición
        $administrativo = Administrativo::where('persona_id', $id)->first();  // Suponiendo que 'persona_id' es la relación en Docente


        $privilegios = Privilegio::all();
        $tiposIdentificacion = IdentificacionDocumento::all();
        $estados = Estado::all();


        // Devolver los datos a la vista
        return view('administrativos.edit', compact('persona', 'cargos', 'administrativo', 'privilegios', 'tiposIdentificacion', 'estados'));
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
        $request->validate([
            'nombres_administrativo' => 'required|string|max:255',
            'apellidos_administrativo' => 'required|string|max:255',
            'fotografia_administrativo' => 'nullable|image|mimes:jpeg,png,jpg|max:3000',
           // 'correo_docente' => 'required|email|unique:personas,email,' . $id,

        ]);

        $administrativo = Administrativo::findOrFail($id);

        // Actualizar los campos del docente

        // Verificar si se subió una nueva fotografía
        if ($request->hasFile('fotografia_administrativo')) {
            // Eliminar la imagen anterior si existe
            if ($administrativo->fotografia_administrativo && Storage::exists('public/' . $administrativo->fotografia_administrativo)) {
                Storage::delete('public/' . $administrativo->fotografia_administrativo);
            }

            // Guardar la nueva imagen
            $imagen = $request->file('fotografia_administrativo');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $ruta = $imagen->storeAs('public/fotografias', $nombreImagen);

            // Guardar la ruta relativa en la base de datos (sin "public/")
            $administrativo->fotografia_administrativo = str_replace('public/', '', $ruta);
        }
        $administrativo->persona->cmsUser->id_cms_privileges = $request->privilegio_id_administrativo;


        $administrativo->persona->nombres = $request->nombres_administrativo;
        $administrativo->persona->apellidos = $request->apellidos_administrativo;
        $administrativo->persona->genero = $request->genero_administrativo;
        $administrativo->persona->fecha_nacimiento = $request->fecha_nacimiento_administrativo;
        $administrativo->persona->identificacion_documentos_id = $request->id_tipoidentificacion_administrativo;
        $administrativo->persona->num_documento = $request->identificacion_administrativo;
        $administrativo->cargo_id = $request->cargo_id;

        $administrativo->persona->telefono = $request->telefono_administrativo;
        $administrativo->persona->email = $request->correo_administrativo;
        $administrativo->persona->direccion = $request->direccion_administrativo;
        $administrativo->persona->fecha_defuncion = $request->fecha_defuncion_administrativo;

        $administrativo->persona->cmsUser->email = $request->correo_administrativo;



        // Guardar los cambios en la base de datos
        $administrativo->push(); // `push()` guarda las relaciones también

        // Redirigir con un mensaje de éxito
        return redirect()->route('administrativos.index')->with('success', 'Personal Administrativo actualizado exitosamente.');
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
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return redirect()->route('administrativos.index')->with('success', 'Persona eliminada exitosamente.');
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

    public function crearAdministrativo($fotografia, $personaId, $cargo, $estadoId)
    {
        // Crear un nuevo registro de estudiante
        $administrativo = Administrativo::create([
           'fotografia_administrativo' => $fotografia,
            'persona_id' => $personaId,
            'cargo_id' => $cargo,



             'estado_id' => $estadoId
        ]);

        // Verificar si la creación del estudiante falló
        if (!$administrativo->id) {
            return ['error' => 'Hubo un problema al crear el Docente.'];
        }

        // Retornar el ID del estudiante creado
        return $administrativo->id;
    }
}
