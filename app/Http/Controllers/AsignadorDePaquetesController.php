<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\Estudiante;
use App\Models\Matriculacion;
use App\Models\ProductoSeleccionado;
use Illuminate\Http\Request;

class AsignadorDePaquetesController extends Controller
{


    public function index()
    {
       
        // Obtener todas las inscripciones que no tienen productos seleccionados
        $inscripciones = Matriculacion::whereDoesntHave('productosSeleccionados')->get();

      

        return view('asignador_de_paquetes.index', compact('inscripciones'));
    }

    public function pagoaestuiantes($id)
    {
        // Obtener la inscripción del estudiante con el paquete, detalles y productos asociados
        $inscripcion = Matriculacion::with([
         'estudiante', // Cargar la relación con el estudiante
         'paquete.detalles.detallesProductos.producto', // Cargar los productos asociados al paquete a través de los detalles
    ])->findOrFail($id);

        // Pasar la inscripción a la vista para mostrar los datos
        return view('asignador_de_paquetes.create', compact('inscripcion'));
    }

    public function create($id)
    {
        // Obtener la inscripción del estudiante con el paquete, detalles y productos asociados
        $inscripcion = Matriculacion::with([
            'estudiante', // Cargar la relación con el estudiante
            'paquete.detalles.detallesProductos.producto', // Cargar los productos asociados al paquete a través de los detalles
        ])->findOrFail($id);

        // Pasar la inscripción a la vista para mostrar los datos
        return view('asignador_de_paquetes.create', compact('inscripcion'));
    }

    public function store(Request $request)
    {
        // Validación de los datos enviados desde el formulario
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'productos' => 'required|json',
        ]);

        // Verificar si ya existe un convenio para esta inscripción
        $inscripcionId = $request->input('inscripcion_id');
        $productosSeleccionados = json_decode($request->input('productos'), true);

        // Comprobar si ya existen registros en ProductoSeleccionado para esta inscripción
        $existenProductos = ProductoSeleccionado::where('inscripcion_id', $inscripcionId)->exists();
        if ($existenProductos) {
            return redirect()->back()->with('message', 'Ya existe un convenio para esta inscripción.');
        }

        // Guardar los productos seleccionados en ProductoSeleccionado
        foreach ($productosSeleccionados as $producto) {
            ProductoSeleccionado::create([
                'inscripcion_id' => $inscripcionId,
                'detalle_id' => $producto['id'], // Asegúrate de que el ID sea correcto
                'precio' => $producto['precio'],
            ]);
        }

        // Redirigir con un mensaje de éxito
        return redirect()->route('asignador_de_paquetes.index')->with('success', 'Paquete asignado exitosamente.');
    }
    

    


   

}
