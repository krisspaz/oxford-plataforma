<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Parentesco;
use App\Models\IdentificacionDocumento;
use App\Models\CMSUser;
use App\Models\Privilegio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AjustePersonaController extends Controller
{
    /**
     * Mostrar una lista de personas.
     */
    public function index()
    {
        $personas = Persona::with(['parentesco', 'identificacionDocumento'])->get();
        return view('ajustes.personas.index', compact('personas'));
    }

    /**
     * Mostrar el formulario para editar una persona.
     */
    public function edit($id)
    {
        $persona = Persona::with(['parentesco', 'identificacionDocumento', 'cmsUser'])->findOrFail($id);
        $parentescos = Parentesco::all();
        $documentos = IdentificacionDocumento::all();
        $privilegios = Privilegio::all();

        return view('ajustes.personas.edit', compact('persona', 'parentescos', 'documentos', 'privilegios'));
    }

    /**
     * Actualizar la información de una persona.
     */
    public function update(Request $request, $id)
    {

        $docId = IdentificacionDocumento::find($request->identificacion_documentos_id);
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'genero' => 'required',
            'profesion' => 'nullable|string|max:100',
            'estado_civil' => 'nullable|string|max:50',
            'apellido_casada' => 'nullable|string|max:100',
            'identificacion_documentos_id' => 'required|exists:tb_identificacion_documentos,id',
             // identificaciones
            'num_documento' => [
                'required',
                'string',
                'max:' . ($docId->max_digitos),

            ],
            'identificacion_documentos_id' => 'required|exists:tb_identificacion_documentos,id',
            'fecha_nacimiento' => 'nullable|date',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_defuncion' => 'nullable|date',
            'parentesco_id' => 'nullable|exists:tb_parentescos,id',
        ]);

        $persona = Persona::findOrFail($id);


        // Guardar email anterior para buscar usuario
        $emailAnterior = $persona->email;

        // Actualizar los datos de la persona
        $persona->update($request->all());

        // Si el correo fue cambiado, actualizar en CMSUser
        if ($request->filled('email') && $request->email !== $emailAnterior) {
            $usuario = CMSUser::where('email', $emailAnterior)->first();
            if ($usuario) {
                $usuario->email = $request->email;
                $usuario->save();
            }
        }

        return redirect()->route('ajuste-persona.index')->with('success', 'Persona actualizada correctamente.');
    }

    /**
 * Mostrar el formulario para crear una nueva persona.
 */
    public function create()
    {
        $parentescos = Parentesco::all();
        $documentos = IdentificacionDocumento::all();
        $privilegios = Privilegio::all();

        return view('ajustes.personas.create', compact('parentescos', 'documentos', 'privilegios'));
    }

    /**
     * Almacenar una nueva persona en la base de datos.
     */
    public function store(Request $request)
    {

        //dd($request->all());

        // Validación de los datos del formulario

        $docId = IdentificacionDocumento::find($request->identificacion_documentos_id);
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'genero' => 'required',
            'profesion' => 'nullable|string|max:100',
            'estado_civil' => 'nullable|string|max:50',
            'apellido_casada' => 'nullable|string|max:100',

             // identificaciones
                'num_documento' => [
                    'required',
                    'string',
                    'max:' . ($docId->max_digitos),
                    'unique:personas,num_documento',
                ],
                'identificacion_documentos_id' => 'required|exists:tb_identificacion_documentos,id',

            'fecha_nacimiento' => 'nullable|date',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_defuncion' => 'nullable|date',
            'parentesco_id' => 'nullable|exists:tb_parentescos,id',
        ]);



        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Crear la persona
            $persona = Persona::create($request->except('crear_usuario')); // Excluyendo el checkbox si no es necesario en la creación

            // Verificar si se marcó el checkbox para la creación de usuario
            if ($request->has('crear_usuario')) {
                $usuarioId = $this->crearUsuarios(
                    $request->nombres . " " . $request->apellidos,
                    $request->email,
                    $request->privilegio_id,
                    $path ?? null // Asegúrate de que $path esté definido si estás usando una imagen
                );

                // Opcional: asociar el usuario creado con la persona
                $persona->cms_users_id = $usuarioId; // Si es necesario, guarda el id del usuario en la persona
                $persona->save();
            }

            // Si todo fue bien, confirmar la transacción
            DB::commit();

            // Redirigir con mensaje de éxito
            return redirect()->route('ajuste-persona.index')->with('success', 'Persona creada correctamente.');

        } catch (\Exception $e) {
            // Si ocurre un error, revertir la transacción
            DB::rollBack();

            // Loguear el error para más detalles
            \Log::error('Error al realizar la inscripción: ' . $e->getMessage(), ['exception' => $e]);

            // Redirigir de vuelta con el mensaje de error
            return back()->with('error', 'Ocurrió un error al realizar la inscripción: ' . $e->getMessage());
        }

    }

    /**
     * Mostrar los detalles de una persona.
     */
    public function show($id)
    {
        $persona = Persona::with(['parentesco', 'identificacionDocumento'])->findOrFail($id);

        return view('ajustes.personas.show', compact('persona'));
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

    public function destroy($id)
    {
        try {
            // Buscar la persona por su ID
            $persona = Persona::findOrFail($id);

            // Verificar si la persona tiene un usuario asociado
            if ($persona->cms_users_id) {
                // Eliminar el usuario asociado a la persona
                $usuario = CMSUser::find($persona->cms_users_id);
                if ($usuario) {
                    $usuario->delete();
                }
            }

            // Eliminar la persona
            $persona->delete();

            // Redirigir con mensaje de éxito
            return redirect()->route('ajuste-persona.index')->with('success', 'Persona y usuario eliminados correctamente.');

        } catch (\Exception $e) {
            // Si ocurre un error, loguear y redirigir con mensaje de error
            \Log::error('Error al eliminar la persona y el usuario: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar la persona y el usuario: ' . $e->getMessage());
        }
    }


}
