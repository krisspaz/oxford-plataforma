<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Cgshge;
use App\Models\Estado;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AjustesEstudiantesController extends Controller
{
    /**
     * Display a listing of the estudiantes.
     */
    public function index()
    {
        // Cargamos estudiantes con sus historiales
        $estudiantes = Estudiante::with(['persona', 'estado', 'academicos', 'medicos'])->get();
        return view('ajustes.estudiantes.index', compact('estudiantes'));
    }

    /**
     * Show the form for creating a new estudiante.
     */
    public function create()
    {
        // Obtén datos necesarios para selects (personas, niveles, grados, estados)
        $personas = Persona::all();
 
        $cgshges = Cgshge::all();
        $estados =  Estado::pluck('estado', 'id');

       

        return view('ajustes.estudiantes.create', compact('personas', 'cgshges', 'estados'));
    }

    /**
     * Store a newly created estudiante in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fotografia_estudiante' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'persona_id' => 'required|exists:personas,id',
            'carnet' => 'required|string|unique:estudiantes,carnet',
            'cgshges_id' => 'required|exists:pv_cgshges,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);
    
        try {
            DB::transaction(function () use ($request) {
    
                if ($request->hasFile('fotografia_estudiante')) {
                    $file = $request->file('fotografia_estudiante');
                    $path = $file->store('fotografias_estudiantes', 'public');
                } else {
                    $path = null;
                }
    
                // Ahora arma manualmente el array de datos
                $data = [
                    'fotografia_estudiante' => $path,
                    'persona_id' => $request->persona_id,
                    'carnet' => $request->carnet,
                    'cgshges_id' => $request->cgshges_id,
                    'estado_id' => $request->estado_id,
                ];
    
                Estudiante::create($data);
    
            });
    
            return redirect()->route('ajustes_estudiantes.index')
                             ->with('success', 'Estudiante creado correctamente.');
    
        } catch (\Exception $e) {
            Log::error('Error al crear estudiante: '.$e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Ocurrió un error al crear el estudiante.');
        }
    }
    

    /**
     * Display the specified estudiante.
     */
    public function show($id)
    {
        $estudiante = Estudiante::with(['persona', 'nivel', 'cgshges', 'estado', 'academicos', 'medicos'])
                         ->findOrFail($id);

        return view('ajustes.estudiantes.show', compact('estudiante'));
    }

    /**
     * Show the form for editing the specified estudiante.
     */
    public function edit($id)
    {
        $estudiante = Estudiante::with(['persona'])->findOrFail($id);

        $personas = Persona::all();
 
        $cgshges = Cgshge::all();
        $estados =  Estado::pluck('estado', 'id');

        return view('ajustes.estudiantes.edit', compact(
            'estudiante',
            'personas',
            'cgshges',
            'estados'
        ));
    }

    /**
     * Update the specified estudiante in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fotografia_estudiante' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'persona_id' => 'required|exists:personas,id',
            'carnet' => "required|string|unique:estudiantes,carnet,{$id}",
            'cgshges_id' => 'required|exists:pv_cgshges,id',
        ]);
    
        try {
            DB::transaction(function () use ($request, $id) {
                $estudiante = Estudiante::findOrFail($id);
    
                $data = $request->only([
                    'persona_id', 'carnet', 'cgshges_id', 'estado_id'
                ]);
    
                if ($request->hasFile('fotografia_estudiante')) {
                    $file = $request->file('fotografia_estudiante');
                    $path = $file->store('fotografias_estudiantes', 'public');
                    $data['fotografia_estudiante'] = $path;
                }
    
                $estudiante->update($data);
            });
    
            return redirect()->route('ajustes_estudiantes.index')
                             ->with('success', 'Estudiante actualizado correctamente.');
    
        } catch (\Exception $e) {
            Log::error('Error al actualizar estudiante: '.$e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Ocurrió un error al actualizar el estudiante.');
        }
    }
    

    /**
     * Remove the specified estudiante from storage.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $estudiante = Estudiante::findOrFail($id);
                // Borrar historiales asociados
                $estudiante->academicos()->delete();
                $estudiante->medicos()->delete();
                // Borrar estudiante
                $estudiante->delete();
            });

            return redirect()->route('ajustes_estudiantes.index')
                             ->with('success', 'Estudiante eliminado correctamente.');

        } catch (\Exception $e) {
            // Registrar el error completo en los logs
            Log::error('Error al eliminar estudiante: '.$e->getMessage());
            
            // Mostrar el error detallado al usuario (opcionalmente se puede ocultar en producción)
            $errorMessage = env('APP_ENV') == 'production' ? 'Ocurrió un error al eliminar el estudiante.' : $e->getMessage();
            
            return redirect()->back()
                             ->with('error', $errorMessage); // El mensaje es enviado a la vista
        }
    }
}
