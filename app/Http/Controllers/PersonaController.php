<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\IdentificacionDocumento;
use App\Models\Municipio;
use App\Models\Departamento;
use App\Models\Pais;
use App\Models\Direccion;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonaController extends Controller
{
    // Mostrar todas las personas
    public function index()
    {
        $personas = Persona::all();  // Traer todas las personas
        return view('personas.index', compact('personas'));
    }

    // Mostrar el formulario para crear una nueva persona
    public function create()
    {
        $identificacionDocumentos = IdentificacionDocumento::all();
        $departamentos= Departamento::all();
        $municipios = Municipio::all();
        $paises = Pais::all();
        return view('personas.create', compact('identificacionDocumentos', 'departamentos', 'municipios', 'paises'));
    }

    public function create2()
    {
        $identificacionDocumentos = IdentificacionDocumento::all();
        $departamentos= Departamento::all();
        $municipios = Municipio::all();
        $paises = Pais::all();
        return view('personas.inscripcion2', compact('identificacionDocumentos', 'departamentos', 'municipios', 'paises'));
    }

    // Guardar una nueva persona en la base de datos
    public function store(Request $request)
    {
        $user = CRUDBooster::me();

        $messages = [
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, elige otro.',
            'fotografia.mimes' => 'La fotografía debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'fotografia.max' => 'La fotografía no debe exceder los 2 MB.',
            'num_documento.unique'  => 'El Número de Identificación ya está registrado. Por favor, elige otro.',
        ];

        // Validar los datos de entrada para cada persona (padre, madre, encargado)
        $request->validate([
            'padre.nombres' => 'required|string|max:255',
            'padre.apellidos' => 'required|string|max:255',
            'padre.num_documento' => 'required|string|unique:tb_personas,num_documento',
            'padre.email' => 'required|email|unique:tb_personas,email',
            'madre.nombres' => 'required|string|max:255',
            'madre.apellidos' => 'required|string|max:255',
            'madre.num_documento' => 'required|string|unique:tb_personas,num_documento',
            'madre.email' => 'required|email|unique:tb_personas,email',
            'encargado.nombres' => 'required|string|max:255',
            'encargado.apellidos' => 'required|string|max:255',
            'encargado.num_documento' => 'required|string|unique:tb_personas,num_documento',
            'encargado.email' => 'required|email|unique:tb_personas,email',
        ], $messages);

        // Función auxiliar para subir la fotografía
        $storeFotografia = function ($file) {
            return $file ? basename($file->store('public/fotografias')) : null;
        };

        // Crear el registro del padre
        $padre = Persona::create([
            'fotografia' => $storeFotografia($request->file('padre.fotografia')),
            'nombres' => $request->input('padre.nombres'),
            'apellidos' => $request->input('padre.apellidos'),
            'genero' => $request->input('padre.genero'),
            'estado_civil' => $request->input('padre.estado_civil'),
            'apellido_casada' => $request->input('padre.apellido_casada'),
            'nacionalidad' => $request->input('padre.nacionalidad'),
            'identificacion_documentos_id' => $request->input('padre.identificacion_documentos_id'),
            'num_documento' => $request->input('padre.num_documento'),
            'lugar_emision' => $request->input('padre.lugar_emision'),
            'lugar_nacimiento' => $request->input('padre.lugar_nacimiento'),
            'fecha_nacimiento' => $request->input('padre.fecha_nacimiento'),
            'nit' => $request->input('padre.nit'),
            'email' => $request->input('padre.email'),
            'tipo_sangre' => $request->input('padre.tipo_sangre'),
            'cms_users_id' => $user->id,
        ]);

        // Crear el registro de la madre
        $madre = Persona::create([
            'fotografia' => $storeFotografia($request->file('madre.fotografia')),
            'nombres' => $request->input('madre.nombres'),
            'apellidos' => $request->input('madre.apellidos'),
            'genero' => $request->input('madre.genero'),
            'estado_civil' => $request->input('madre.estado_civil'),
            'apellido_casada' => $request->input('madre.apellido_casada'),
            'nacionalidad' => $request->input('madre.nacionalidad'),
            'identificacion_documentos_id' => $request->input('madre.identificacion_documentos_id'),
            'num_documento' => $request->input('madre.num_documento'),
            'lugar_emision' => $request->input('madre.lugar_emision'),
            'lugar_nacimiento' => $request->input('madre.lugar_nacimiento'),
            'fecha_nacimiento' => $request->input('madre.fecha_nacimiento'),
            'nit' => $request->input('madre.nit'),
            'email' => $request->input('madre.email'),
            'tipo_sangre' => $request->input('madre.tipo_sangre'),
            'cms_users_id' => $user->id,
        ]);

        // Crear el registro del encargado
        $encargado = Persona::create([
            'fotografia' => $storeFotografia($request->file('encargado.fotografia')),
            'nombres' => $request->input('encargado.nombres'),
            'apellidos' => $request->input('encargado.apellidos'),
            'genero' => $request->input('encargado.genero'),
            'estado_civil' => $request->input('encargado.estado_civil'),
            'apellido_casada' => $request->input('encargado.apellido_casada'),
            'nacionalidad' => $request->input('encargado.nacionalidad'),
            'identificacion_documentos_id' => $request->input('encargado.identificacion_documentos_id'),
            'num_documento' => $request->input('encargado.num_documento'),
            'lugar_emision' => $request->input('encargado.lugar_emision'),
            'lugar_nacimiento' => $request->input('encargado.lugar_nacimiento'),
            'fecha_nacimiento' => $request->input('encargado.fecha_nacimiento'),
            'nit' => $request->input('encargado.nit'),
            'email' => $request->input('encargado.email'),
            'tipo_sangre' => $request->input('encargado.tipo_sangre'),
            'cms_users_id' => $user->id,
        ]);

        Direccion::create([

            'municipio_id' => $request->input('municipio_id'),
            'direccion' => $request->input('direccion'),
            'telefono_casa' => $request->input('telefono_casa'),
            'telefono_mobil' => $request->input('telefono_mobil'),
            'pais__id' => $request->input('pais_id'),

        ]);

        // Crear el registro en la tabla pv_padres_tutores
        PvPadresTutores::create([
            'padre_id' => $padre->id,
            'madre_id' => $madre->id,
            'encargado_id' => $encargado->id,
        ]);

        return redirect()->route('personas.index')->with('success', 'Padre, Madre y Encargado registrados correctamente.');
    }

    // Mostrar los detalles de una persona
    public function show(Persona $persona)
    {
        return view('personas.show', compact('persona'));
    }

    // Mostrar el formulario para editar una persona
    public function edit(Persona $persona)
    {
        $identificacionDocumentos = IdentificacionDocumento::all();
        $departamentos= Departamento::all();
        $municipios = Municipio::all();
        $paises = Pais::all();
        return view('personas.edit', compact('persona', 'identificacionDocumentos', 'departamentos', 'municipios', 'paises'));
    }

    // Actualizar los datos de una persona en la base de datos
    public function update(Request $request, Persona $persona)
    {
        // Validar los datos de entrada
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:tb_personas,email,' . $persona->id,
            'fotografia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Subir la fotografía si se proporcionó
        if ($request->hasFile('fotografia')) {
            // Eliminar la fotografía antigua si existe
            if ($persona->fotografia) {
                Storage::delete('public/' . $persona->fotografia);
            }

            // Guardar la nueva fotografía
            $path = $request->file('fotografia')->store('public/fotografias');
            $fotografia = basename($path);
        } else {
            $fotografia = $persona->fotografia;  // Mantener la fotografía actual
        }

        // Actualizar los datos de la persona
        $persona->update([
            'fotografia' => $fotografia,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'genero' => $request->genero,
            'estado_civil' => $request->estado_civil,
            'apellido_casada' => $request->apellido_casada,
            'nacionalidad' => $request->nacionalidad,
            'identificacion_documentos_id' => $request->identificacion_documentos_id,
            'num_documento' => $request->num_documento,
            'lugar_emision' => $request->lugar_emision,
            'lugar_nacimiento' => $request->lugar_nacimiento,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'nit' => $request->nit,
            'email' => $request->email,
            'municipio_id' => $request->municipio_id,
            'direccion' => $request->direccion,
            'telefono_casa' => $request->telefono_casa,
            'telefono_mobil' => $request->telefono_mobil,
            'fecha_defuncion' => $request->fecha_defuncion,
            'pais_origen_id' => $request->pais_origen_id,
            'tipo_sangre' => $request->tipo_sangre,
           
        ]);

        return redirect()->route('personas.index')->with('success', 'Persona actualizada exitosamente.');
    }

    // Eliminar una persona de la base de datos
    public function destroy(Persona $persona)
    {
        // Eliminar la fotografía si existe
        if ($persona->fotografia) {
            Storage::delete('public/' . $persona->fotografia);
        }

        // Eliminar la persona
        $persona->delete();

        return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente.');
    }


    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)->get();
        return response()->json($municipios);
    }

  


}
