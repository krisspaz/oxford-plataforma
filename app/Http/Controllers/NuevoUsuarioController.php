<?php

namespace App\Http\Controllers;

use App\Models\CMSUser;
use App\Models\Persona;
use App\Models\CMSPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class NuevoUsuarioController extends Controller
{
    /**
    * Muestra todos los usuarios.
    */
    public function index()
    {
        $usuarios = CMSUser::with('cmsPrivilege')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario de creación de un nuevo usuario.
     */
    public function create()
    {
        $personas = Persona::all();
        $privilegios = CMSPrivilege::all();
        return view('usuarios.create', compact('privilegios', 'personas'));
    }

    /**
     * Almacena un nuevo usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:cms_users,email',
            'password' => 'required|string|min:8|confirmed',
            'id_cms_privileges' => 'required',
            'photo' => 'nullable|image|max:2048',
            'persona_id' => 'required|exists:personas,id'
        ]);
    
        $data = $request->except(['password', 'photo', 'password_confirmation']);
        $data['password'] = Hash::make($request->password);
    
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('usuarios', 'public');
        }
    
        // Crear el usuario
        $user = CMSUser::create($data);
    
        // Relacionar la persona con el usuario
        $persona = Persona::findOrFail($request->persona_id);
        $persona->update([
            'email' => $user->email,
            'cms_users_id' => $user->id,
        ]);
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra los detalles de un usuario específico.
     */
    public function show($id)
    {
        $usuario = CMSUser::with('cmsPrivilege')->findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Muestra el formulario de edición de un usuario.
     */
    public function edit($id)
    {
        $usuario = CMSUser::findOrFail($id);
        $privilegios = CMSPrivilege::all();
        $personas = Persona::all();
        return view('usuarios.edit', compact('usuario', 'privilegios', 'personas', ));
    }

    /**
     * Actualiza la información de un usuario.
     */
    public function update(Request $request, $id)
    {
        $usuario = CMSUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
            'id_cms_privileges' => 'required|exists:cms_privileges,id',
            'photo' => 'nullable|image|max:2048',
            'persona_id' => 'required|exists:personas,id'
        ]);

        $data = $request->except(['password', 'photo', 'password_confirmation']);

        // Actualiza la contraseña si fue enviada
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Actualiza la foto si fue enviada
        if ($request->hasFile('photo')) {
            if ($usuario->photo) {
                Storage::disk('public')->delete($usuario->photo);
            }
            $data['photo'] = $request->file('photo')->store('usuarios', 'public');
        }

        // Guarda los cambios del usuario
        $usuario->update($data);

        // Obtiene la persona relacionada
        $persona = Persona::findOrFail($request->persona_id);

        // Solo actualiza el email si fue modificado
        if ($usuario->wasChanged('email')) {
            $persona->email = $usuario->email;
        }

        // Siempre actualiza la relación con el usuario
        $persona->cms_users_id = $usuario->id;
        $persona->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy($id)
    {
        $usuario = CMSUser::findOrFail($id);

        if ($usuario->photo) {
            Storage::disk('public')->delete($usuario->photo);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
