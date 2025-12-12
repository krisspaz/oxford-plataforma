<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matriculacion;
use App\Models\Estudiante;
use App\Models\Cgshge;
use App\Models\Paquete;
use App\Models\Estado;
use App\Models\CMSUser;
use App\Models\ContratoController;
use Illuminate\Support\Facades\Log;

class AjustesInscripcionesController extends Controller
{
    
  

    // Muestra la lista de inscripciones
    public function index()
    {
        $inscripciones = Matriculacion::with(['estudiante', 'cgshges', 'paquete', 'estado', 'cmsUser'])->get();
       
        return view('ajustes.inscripciones.index', compact('inscripciones'));
    }

    // Muestra el formulario para crear una nueva inscripción
    public function create()
    {
        $estudiantes = Estudiante::all();
        $cgshges = Cgshge::all();
        $paquetes = Paquete::all();
        $estados = Estado::all();
        $usuarios = CMSUser::all();

        $anioActual = now()->year;

        // Calcular los años requeridos
        $anios = [
            
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
           
        ];
        return view('ajustes.inscripciones.create', compact('estudiantes', 'cgshges', 'paquetes', 'estados', 'usuarios', 'anios'));
    }

    // Almacena una nueva inscripción en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'cgshges_id' => 'required|exists:pv_cgshges,id',
            'paquete_id' => 'required|exists:paquetes,id',
            'fecha_inscripcion' => 'required|date',
            'ciclo_escolar' => 'required|string',
            'estado_id' => 'required|exists:tb_estados,id',
            'cms_users_id' => 'required|exists:cms_users,id',
        ]);

        $inscripcion = Matriculacion::create($request->all());

       

        $contratoController = new ContratoController();
        $contratoController->generarContrato($inscripcion);

        return redirect()->route('ajustes_inscripciones.index')->with('success', 'Inscripción creada con éxito.');
    }

    // Muestra el formulario para editar una inscripción existente
    public function edit($id)
    {
        $inscripcion = Matriculacion::findOrFail($id);
        $estudiantes = Estudiante::all();
        $cgshges = Cgshge::all();
        $paquetes = Paquete::all();
        $estados = Estado::all();
        $usuarios = CMSUser::all();

        $anioActual = now()->year;

        // Calcular los años requeridos
        $anios = [
            
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
           
        ];
        return view('ajustes.inscripciones.edit', compact('inscripcion', 'estudiantes', 'cgshges', 'paquetes', 'estados', 'anios', 'usuarios'));
    }

    // Actualiza la inscripción en la base de datos
    public function update(Request $request, $id)
    {
        try {
            // Validar datos
            $request->validate([
                'estudiante_id' => 'required|exists:estudiantes,id',
                'cgshges_id' => 'required|exists:pv_cgshges,id',
                'paquete_id' => 'required|exists:paquetes,id',
                'fecha_inscripcion' => 'required|date',
                'ciclo_escolar' => 'required|string',
                'estado_id' => 'required|exists:tb_estados,id',
                'cms_users_id' => 'required|exists:cms_users,id',
            ]);
    
            // Buscar e actualizar
            $inscripcion = Matriculacion::findOrFail($id);
            $inscripcion->update($request->all());
    
            return redirect()->route('ajustes_inscripciones.index')->with('success', 'Inscripción actualizada con éxito.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error de validación
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
            
        } catch (\Exception $e) {
            // Otros errores
            Log::error('Error al actualizar inscripción: '.$e->getMessage());
    
            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar la inscripción. Por favor, inténtelo de nuevo.')
                ->withInput();
        }
    }

    // Muestra los detalles de una inscripción
    public function show($id)
    {
        $inscripcion = Matriculacion::with(['estudiante', 'cgshges', 'paquete', 'estado', 'cmsUser'])->findOrFail($id);
        return view('ajustes.inscripciones.show', compact('inscripcion'));
    }

    // Elimina una inscripción de la base de datos
    public function destroy($id)
    {
        $inscripcion = Matriculacion::findOrFail($id);
        $inscripcion->delete();

        return redirect()->route('ajustes_inscripciones.index')->with('success', 'Inscripción eliminada con éxito.');
    }
}
