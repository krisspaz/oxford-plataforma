<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleProducto;
use App\Models\PaqueteDetalle;
use App\Models\Producto;

class DetalleProductoController extends Controller
{
    /**
     * Muestra una lista de los recursos.
     */
    public function index()
    {
        $detalleProductos = DetalleProducto::with(['paqueteDetalle', 'producto'])->get();
        return view('detalle_productos.index', compact('detalleProductos'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $paqueteDetalles = PaqueteDetalle::all();
        $productos = Producto::all();
        return view('detalle_productos.create', compact('paqueteDetalles', 'productos'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'paquete_detalle_id' => 'required|exists:paquete_detalles,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        DetalleProducto::create($validated);
        return redirect()->route('detalle_productos.index')->with('success', 'Detalle del producto creado correctamente.');
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(DetalleProducto $detalleProducto)
    {
        $detalleProducto->load(['paqueteDetalle', 'producto']);
        return view('detalle_productos.show', compact('detalleProducto'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(DetalleProducto $detalleProducto)
    {
        $paqueteDetalles = PaqueteDetalle::all();
        $productos = Producto::all();
        return view('detalle_productos.edit', compact('detalleProducto', 'paqueteDetalles', 'productos'));
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     */
    public function update(Request $request, DetalleProducto $detalleProducto)
    {
        $validated = $request->validate([
            'paquete_detalle_id' => 'required|exists:paquete_detalles,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalleProducto->update($validated);
        return redirect()->route('detalle_productos.index')->with('success', 'Detalle del producto actualizado correctamente.');
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     */
    public function destroy(DetalleProducto $detalleProducto)
    {
        $detalleProducto->delete();
        return redirect()->route('detalle_productos.index')->with('success', 'Detalle del producto eliminado correctamente.');
    }
}
