<?php

namespace App\Http\Controllers;

use App\Models\PvEstudianteContrato;
use App\Models\Estudiante;
use App\Models\Contrato;
use Illuminate\Http\Request;

class AjustesContratoController extends Controller
{
    
    public function index()
    {
        // Obtener todos los contratos de estudiantes
        $contratos = PvEstudianteContrato::with(['estudiante', 'contrato'])->get();
        return view('ajustes.contrato.index', compact('contratos'));
    }

    /**
     * Muestra el formulario para crear un nuevo contrato para un estudiante.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estudiantes = Estudiante::all();  // Obtener todos los estudiantes
        $contratos = Contrato::all();  // Obtener todos los contratos
        return view('ajustes.contrato.create', compact('estudiantes', 'contratos'));
    }

    /**
     * Almacena un nuevo contrato para un estudiante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'contrato_id' => 'required|exists:contratos,id',
            'contrato_firmado' => 'required|boolean',
            'estado' => 'required|string|max:255',
        ]);

        // Crear el contrato del estudiante
        PvEstudianteContrato::create([
            'estudiante_id' => $request->estudiante_id,
            'contrato_id' => $request->contrato_id,
            'contrato_firmado' => $request->contrato_firmado,
            'estado' => $request->estado,
        ]);

        return redirect()->route('ajustes_contrato.index')->with('success', 'Contrato creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un contrato.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contrato = PvEstudianteContrato::findOrFail($id);
        $estudiantes = Estudiante::all();
        $contratos = Contrato::all();
        return view('ajustes.contrato.edit', compact('contrato', 'estudiantes', 'contratos'));
    }

    /**
     * Actualiza un contrato existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'contrato_firmado' => 'required|file|mimes:pdf|max:2048',
            ]);
        
            $contrato = PvEstudianteContrato::findOrFail($id);
        
            if ($request->hasFile('contrato_firmado')) {
                $file = $request->file('contrato_firmado');
                $path = $file->store('contratos_firmados', 'public'); // Guardar en carpeta 'public/contratos_firmados'
        
                // Actualizar la columna contrato_firmado con la ruta del archivo
                $contrato->contrato_firmado = $path;
                $contrato->estado = 'Vigente';  // Cambiar estado si es necesario
                $contrato->save();
        
                return redirect()->route('ajustes_contrato.index')->with('success', 'Contrato actualizado exitosamente.');
            }
    
           
        } catch (\Exception $e) {
            // Capturar cualquier error y redireccionar mostrando el mensaje
            return redirect()->back()
                ->with('error', 'Ocurrió un error: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    /**
     * Elimina un contrato de estudiante.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contrato = PvEstudianteContrato::findOrFail($id);
        $contrato->delete();

        return redirect()->route('ajustes_contrato.index')->with('success', 'Contrato eliminado exitosamente.');
    }
}
