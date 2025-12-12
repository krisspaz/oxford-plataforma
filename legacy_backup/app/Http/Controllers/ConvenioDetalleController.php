<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvenioDetalle;
use App\Models\Convenio;
use App\Models\Cuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use App\Models\ProductoSeleccionado;

class ConvenioDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Obtener todos los convenios que no tienen detalles registrados en convenios_detalles
        $conveniosSinDetalles = Convenio::with(['inscripcion.estudiante', 'inscripcion.paquete', 'inscripcion.productosSeleccionados.detalle'])
        ->whereDoesntHave('detalles') // Filtra los convenios sin registros en convenios_detalles
        ->get();



        return view('convenios_detalles.index', compact('conveniosSinDetalles'));
    }

    public function ConDetalles()
    {

        // Obtener todos los convenios que tienen detalles registrados en convenios_detalles
        $conveniosConDetalles = Convenio::with(['inscripcion.estudiante', 'inscripcion.paquete', 'inscripcion.productosSeleccionados.detalle'])
        ->whereHas('detalles') // Filtra los convenios que tienen registros en convenios_detalles
        ->get();

        return view('convenios_detalles.ConDetalles', compact('conveniosConDetalles'));
    }

    public function MostrarCuotas($id)
    {

        // Obtener el convenio con las cuotas
        $convenio = Convenio::with(['cuotas.productoSeleccionado.detalle'])->findOrFail($id);

        return view('convenios_detalles.mostrar_cuotas', compact('convenio'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        // Obtener el convenio por ID y cargar sus relaciones
        $convenio = Convenio::with('inscripcion.productosSeleccionados.detalle')->findOrFail($id);

        return view('convenios_detalles.create', compact('convenio'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {




        // Obtener los datos enviados

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $montoTotal = $request->input('monto_total');

        $cuotas = $request->input('cuotas');
        $convenioId = $request->input('convenio_id');

        // Recorrer cada monto total y guardar los detalles
        foreach ($montoTotal as $productoId => $monto) {
            $producto = ProductoSeleccionado::find($productoId);

            if ($producto) {
                // Guardar los detalles del convenio
                // Asegúrate de que el campo 'productos_seleccionados_id' esté correctamente asignado
                ConvenioDetalle::create([
                    'convenio_id' => $convenioId,
                    'productos_seleccionados_id' => $productoId,  // Este es el valor del ID del producto seleccionado
                    'cantidad_cuotas' => $cuotas[$productoId] ?? 1,  // Usar cuotas si se define, sino poner 1
                    'monto_total' => $monto,   // Usar el monto total enviado desde el frontend
                ]);

                // Calcular el monto de cuota para este producto
                $montoCuota = $monto / ($cuotas[$productoId] ?? 1); // Dividir el monto total por la cantidad de cuotas

                // Crear las cuotas para este producto
                for ($i = 1; $i <= ($cuotas[$productoId] ?? 1); $i++) {

                    Cuota::create([
                        'convenio_id' => $convenioId,
                        'productos_seleccionados_id' => $productoId, // Aseguramos que el id del producto se almacene
                        'monto_cuota' => $montoCuota,
                        'fecha_vencimiento' =>   $fechaInicio->copy()->addMonths($i - 1),
                    ]);
                }
            }
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('convenios_detalles.index')->with('success', 'Detalles del convenio guardados correctamente.');

    }





    /**
     * Display the specified resource.
     */
    public function show(ConvenioDetalle $convenioDetalle)
    {
        return view('convenios_detalles.show', compact('convenioDetalle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Obtener el convenio por ID y cargar sus relaciones
        $convenio = Convenio::with('inscripcion.productosSeleccionados.detalle')->findOrFail($id);

        return view('convenios_detalles.edit', compact('convenio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        // Obtener el convenio
        $convenio = Convenio::findOrFail($id);

        // Verificar si existen detalles y cuotas antes de eliminarlas
        $detallesEliminados = $convenio->detalles()->exists();
        $cuotasEliminadas = $convenio->cuotas()->exists();

        if ($detallesEliminados) {
            // Eliminar todos los detalles del convenio
            $convenio->detalles()->delete();
        }

        if ($cuotasEliminadas) {
            // Eliminar todas las cuotas asociadas al convenio
            $convenio->cuotas()->delete();
        }

        // Verificar que ambos se eliminaron correctamente
        if ($detallesEliminados && $cuotasEliminadas) {
            // Obtener los datos enviados
            $montoTotal = $request->input('monto_total');
            $cuotas = $request->input('cuotas');
            $convenioId = $request->input('convenio_id');

            // Recorrer cada monto total y guardar los detalles
            foreach ($montoTotal as $productoId => $monto) {
                $producto = ProductoSeleccionado::find($productoId);

                if ($producto) {
                    // Guardar los detalles del convenio
                    ConvenioDetalle::create([
                        'convenio_id' => $convenioId,
                        'productos_seleccionados_id' => $productoId,  // Este es el valor del ID del producto seleccionado
                        'cantidad_cuotas' => $cuotas[$productoId] ?? 1,  // Usar cuotas si se define, sino poner 1
                        'monto_total' => $monto,   // Usar el monto total enviado desde el frontend
                    ]);

                    // Calcular el monto de cuota para este producto
                    $montoCuota = $monto / ($cuotas[$productoId] ?? 1); // Dividir el monto total por la cantidad de cuotas
                    $fechaInicio = Carbon::parse($request->fecha_inicio); // Convertir la fecha a un objeto Carbon
                    // Crear las cuotas para este producto
                    for ($i = 1; $i <= ($cuotas[$productoId] ?? 1); $i++) {
                        Cuota::create([
                            'convenio_id' => $convenioId,
                            'productos_seleccionados_id' => $productoId, // Aseguramos que el id del producto se almacene
                            'monto_cuota' => $montoCuota,
                           'fecha_vencimiento' => $fechaInicio->copy()->addMonths($i- 1), // Calcular fecha de vencimiento
                        ]);
                    }
                }
            }
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('convenios_detalles.index')->with('success', 'Detalles del convenio actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        DB::beginTransaction(); // Comienza la transacción

        try {
            // Encuentra el convenio
            $convenio = Convenio::findOrFail($id);

            // Elimina los detalles relacionados con el convenio
            $convenio->detalles()->delete(); // Elimina todos los registros relacionados

            $convenio->cuotas()->delete();
            // Elimina el convenio
            // $convenio->delete();

            DB::commit(); // Confirma la transacción

            return redirect()->route('convenios.index')->with('success', 'Convenio y sus detalles eliminados exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, deshace los cambios

            // Registra el error en los logs
            Log::error('Error al eliminar convenio: ' . $e->getMessage());

            // Muestra el mensaje de error al usuario, incluyendo detalles de la excepción si lo deseas
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el convenio y sus detalles: ' . $e->getMessage());
        }
    }
}
