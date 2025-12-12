<?php

namespace App\Http\Controllers;

use App\Models\ProductoSeleccionado;
use App\Models\Convenio;
use App\Models\PaqueteDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoSeleccionadoController extends Controller
{

    // Método para listar todos los productos seleccionados
    public function index()
    {

        $ngestiones = DB::table('gestionesacademicas')
                ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
                ->where(function ($query) {
                    $query->where('tb_estados.estado', 'Activo')
                        ->orWhere('tb_estados.estado', 'ACTIVO');
                })
                ->select('gestionesacademicas.*')
                ->first(); // <- usa first() en lugar de get()

        $ciclo_escolar = $ngestiones->ciclo_escolar ?? null;

        //  dd($ciclo_escolar);

        $resumenInscripciones = ProductoSeleccionado::selectRaw('
        inscripcion_id,
        COUNT(*) as cantidad_productos,
        SUM(CASE WHEN paquete_detalles.nombre LIKE "%mensualidad%"
                 THEN productos_seleccionados.precio * 1
                 ELSE productos_seleccionados.precio END) as total_precio
    ')
    ->join('paquete_detalles', 'productos_seleccionados.detalle_id', '=', 'paquete_detalles.id')
    ->join('inscripciones', 'productos_seleccionados.inscripcion_id', '=', 'inscripciones.id') // join con la tabla inscripciones
    ->where('inscripciones.ciclo_escolar', $ciclo_escolar) // filtra solo el ciclo escolar activo
    ->groupBy('inscripcion_id')
    ->with('inscripcion')
    ->get();

        return view('productos_seleccionados.index', compact('resumenInscripciones'));

    }


    public function mostrar($inscripcionId)
    {

        // Filtrar productos seleccionados relacionados con la inscripción específica
        $productosSeleccionados = ProductoSeleccionado::with(['inscripcion', 'detalle'])
        ->where('inscripcion_id', $inscripcionId) // Ajusta 'inscripcion_id' al nombre del campo correspondiente
        ->get();

        return view('productos_seleccionados.index', compact('productosSeleccionados'));

    }

    public function crearconvenio()
    {
        Convenio::create([

            'inscripcion_id' => $request->inscripcion_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => 'activo',

        ]);


    }

    // Mostrar el formulario para crear un nuevo producto seleccionado
    public function create()
    {
        return view('productos_seleccionados.create');
    }

    // Almacenar un nuevo producto seleccionado

    public function store(Request $request, $inscripcionId)
    {
        // Validar que se haya seleccionado un producto
        $request->validate([
            'producto_id' => 'required|exists:paquete_detalles,id', // Asegúrate de que el producto existe
        ]);

        // Verificar si el producto ya está seleccionado para esta inscripción
        $productoExistente = ProductoSeleccionado::where('inscripcion_id', $inscripcionId)
            ->where('detalle_id', $request->producto_id)
            ->exists();

        if ($productoExistente) {
            // Si el producto ya ha sido seleccionado, devolvemos un mensaje
            return back()->with('error', 'Este producto ya está seleccionado para esta inscripción.');
        }

        // Obtener el precio del producto desde PaqueteDetalle
        $productoDetalle = PaqueteDetalle::find($request->producto_id);

        // Crear un nuevo producto seleccionado
        ProductoSeleccionado::create([
            'inscripcion_id' => $inscripcionId,     // ID de la inscripción
            'detalle_id' => $request->producto_id,  // ID del producto seleccionado
            'precio' => $productoDetalle->precio,   // Precio del producto
        ]);

        // Redirigir de vuelta a la vista 'show' con un mensaje de éxito
        return redirect()->route('productos_seleccionados.show', $inscripcionId)
            ->with('success', 'Producto añadido correctamente.');
    }



    // Mostrar los detalles de un producto seleccionado
    public function show($inscripcionId)
    {
        // Obtener los productos seleccionados de la inscripción
        $productosSeleccionados = ProductoSeleccionado::with(['detalle'])
        ->where('inscripcion_id', $inscripcionId)
        ->get();

        // Obtener todos los productos disponibles (que no estén ya seleccionados)
        $productosDisponibles2 = PaqueteDetalle::whereNotIn(
            'id',
            $productosSeleccionados->pluck('detalle_id')
        )->get();

        $productosDisponibles = PaqueteDetalle::where('paquete_id', $productosSeleccionados->first()->detalle->paquete_id)
            ->whereNotIn('id', $productosSeleccionados->pluck('detalle_id'))
            ->get();




        return view('productos_seleccionados.show', compact('productosSeleccionados', 'productosDisponibles'));

    }



    // Mostrar el formulario para editar un producto seleccionado
    public function edit($inscripcionId)
    {
        // Obtenemos todos los productos seleccionados asociados a la inscripción
        $productosSeleccionados = ProductoSeleccionado::with(['inscripcion', 'detalle'])
            ->where('inscripcion_id', $inscripcionId)
            ->get();

        if ($productosSeleccionados->isEmpty()) {
            abort(404, 'No hay productos seleccionados para esta inscripción.');
        }

        return view('productos_seleccionados.edit', compact('productosSeleccionados'));
    }

    // Actualizar un producto seleccionado en la base de datos

    public function update(Request $request, $inscripcionId)
    {
        $productosSeleccionados = ProductoSeleccionado::where('inscripcion_id', $inscripcionId)->get();

        if ($productosSeleccionados->isEmpty()) {
            abort(404, 'No hay productos seleccionados para esta inscripción.');
        }

        // Validar los datos recibidos
        $validated = $request->validate([
            'precios.*' => 'required|numeric|min:0',
        ]);

        foreach ($productosSeleccionados as $producto) {
            if (isset($validated['precios'][$producto->id])) {
                $nuevoPrecio = $validated['precios'][$producto->id];

                // Verifica si el nombre del producto es 'mensualidad'
                $nombreProducto = strtolower(optional($producto->detalle)->nombre);

                if (strpos($nombreProducto, 'mensualidad') !== false) {
                    $nuevoPrecio *= 10;
                }

                $producto->update([
                    'precio' => $nuevoPrecio,
                ]);
            }
        }

        return redirect()->route('productos_seleccionados.index')
            ->with('success', 'Productos actualizados correctamente.');
    }


    public function destroy($id)
    {
        // Eliminar un producto seleccionado

        $producto = ProductoSeleccionado::findOrFail($id);

        // Validamos que exista la inscripción relacionada
        if (!$producto->inscripcion_id) {
            return redirect()->route('productos_seleccionados.index')
                ->with('error', 'No se encontró la inscripción asociada al producto.');
        }

        $inscripcionId = $producto->inscripcion_id;

        $producto->delete();

        return redirect()->route('productos_seleccionados.show', $inscripcionId)
            ->with('success', 'Producto eliminado correctamente.');
    }





}
