<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Convenio;
use App\Models\Estudiante;
use App\Models\Matriculacion;
use App\Models\Cuota;

class ConvenioController extends Controller
{
    
    public function index()
    {
        // Obtener inscripciones que tienen productos seleccionados pero no tienen un convenio asociado
        $inscripciones = Matriculacion::has('productosSeleccionados') // Filtrar las que tienen productos seleccionados
        ->doesntHave('convenio') // Filtrar las que no tienen convenio
        ->with(['estudiante.persona', 'paquete', 'estado', 'productosSeleccionados'])
        ->get();

        // Retornar a la vista con las inscripciones
        return view('convenios.index', compact('inscripciones'));
    }



    // Mostrar el formulario para crear un nuevo convenio
    public function create($id)
    {
        $inscripcion = Matriculacion::with(['estudiante', 'paquete.detalles.detallesProductos.producto'])
            ->findOrFail($id);

        return view('convenios.create', compact('inscripcion'));
    }

 
    // Almacenar un nuevo convenio en la base de datos
    public function store(Request $request)
    {
        //validar si existe ya un convenio con la referencia de inscripcion

        // Validación: verificar si ya existe un convenio para esa inscripción
        $existeConvenio = Convenio::where('inscripcion_id', $request->inscripcion_id)->exists();

        if ($existeConvenio) {
            // Si ya existe, redirigir con un mensaje de error
            return redirect()->route('convenios.index')->with('error', 'Ya existe un convenio para esta inscripción.');
        }
     
        // Validar los datos
        $request->validate([
          'inscripcion_id' => 'required|exists:inscripciones,id',
          'fecha_inicio' => 'required|date',
          'fecha_fin' => 'required|date',
          'estado' => 'required|string',
          'productos' => 'nullable|array'
]);

        // Crear el convenio
        $convenio = Convenio::create([
            'inscripcion_id' => $request->inscripcion_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => $request->estado
        ]);

        // Asociar los productos seleccionados al convenio
        if ($request->has('productos')) {
            foreach ($request->productos as $productoId) {
                // Aquí puedes asociar cada producto seleccionado al convenio
                // Puedes agregar registros a la tabla intermedia entre Convenio y Producto
            }
        }

        return redirect()->route('convenios.mostrar')->with('success', 'Convenio asignado correctamente');


    }




    public function store2(Request $request)
    {
   
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'productos' => 'required|array', // Esperamos un array de productos
        ]);

    
    
     
        $contratoController = new ContratoController();
        $pdf = $contratoController->generarContrato($request->estudiante_id);

   

        // Después de generar el contrato, redirige a convenios.index
        //return  $pdf;

   
    


    

    }

    // Mostrar el formulario para editar un convenio existente
    public function edit($id)
    {
        $convenio = Convenio::findOrFail($id); // Buscar el convenio por su ID
        $estudiantes = Estudiante::all(); // Obtener todos los estudiantes disponibles
        return view('convenios.edit', compact('convenio', 'estudiantes')); // Pasar el convenio y los estudiantes a la vista
    }

    // Actualizar un convenio existente en la base de datos
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'monto_total' => 'required|numeric',
            'cantidad_cuotas' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        // Buscar el convenio y actualizarlo con los nuevos datos
        $convenio = Convenio::findOrFail($id);
        $convenio->update($request->all());

        // Generar o actualizar las cuotas
        $convenio->cuotas()->delete(); // Eliminar las cuotas existentes
        $monto_cuota = $request->monto_total / $request->cantidad_cuotas;
        for ($i = 1; $i <= $request->cantidad_cuotas; $i++) {
            Cuota::create([
                'convenio_id' => $convenio->id,
                'monto_cuota' => $monto_cuota,
                'fecha_vencimiento' => now()->addMonths($i),
            ]);
        }

        // Redirigir con un mensaje de éxito
        return redirect()->route('convenios.index')->with('success', 'Convenio actualizado exitosamente.');
    }

    // Mostrar los detalles de un convenio
    public function show($id)
    {
        $convenio = Convenio::with('inscripcion.estudiante', 'cuotas')->findOrFail($id); // Obtener el convenio con sus cuotas y estudiante
        return view('convenios.show', compact('convenio')); // Pasar el convenio a la vista
    }

    // Eliminar un convenio
    public function destroy($id)
    {
        $convenio = Convenio::findOrFail($id); // Buscar el convenio por su ID
        $convenio->cuotas()->delete(); // Eliminar las cuotas asociadas
        $convenio->detalles()->delete();
        $convenio->delete(); // Eliminar el convenio

        // Redirigir con un mensaje de éxito
        return redirect()->route('convenios.index')->with('success', 'Convenio eliminado exitosamente.');
    }

    public function mostrar_convenios()
    {
        // Obtener todos los convenios con sus relaciones
        $convenios = Convenio::with(['inscripcion.estudiante.persona', 'inscripcion.paquete', 'inscripcion.productosSeleccionados'])
                    ->get()
                    ->groupBy(function ($convenio) {
                        return $convenio->inscripcion->estudiante->persona->nombres . ' ' . $convenio->inscripcion->estudiante->persona->apellidos;
                    });

        //   dd($convenios);

        // Retornar la vista con los convenios
        return view('convenios.convenios_con_matricula', compact('convenios'));
    }

}
