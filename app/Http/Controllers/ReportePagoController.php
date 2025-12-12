<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pago;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class ReportePagoController extends Controller
{
    /*
    public function generarPDF()
    {
       // Obtener los pagos necesarios con sus relaciones
       $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])
       ->whereDoesntHave('facturaEmitida')   // Filtra solo los pagos sin factura emitida
       ->whereDoesntHave('reciboEmitido')    // Filtra solo los pagos sin recibo emitido
       ->get();

   // Formatear los datos para la vista
   $datosPagos = $pagos->map(function ($pago) {
       return [
           'id' => $pago->id,
           'convenio_id' => $pago->convenio_id,
           'monto' => $pago->monto,
           'fecha_pago' => $pago->fecha_pago,
           'inscripcion' => $pago->cuotas->first()->productoSeleccionado->inscripcion,
           'tipo_pago' => $pago->tipo_pago,
           'cuotas' => $pago->cuotas->map(function ($cuota) {
               return [
                   'id' => $cuota->id,
                   'monto_cuota' => $cuota->monto_cuota,
                   'estado' => $cuota->estado,
                   'producto_seleccionado' => $cuota->productoSeleccionado ? [
                       'id' => $cuota->productoSeleccionado->id,
                       'nombre' => $cuota->productoSeleccionado->detalle->nombre ?? 'N/A',
                       'precio' => $cuota->productoSeleccionado->precio,
                   ] : null,
               ];
           }),
           'metodos_de_pago' => $pago->pagoMetodos->map(function ($metodo) {
               return [
                   'metodo' => $metodo->metodo_pago,
                   'monto' => $metodo->monto,
                   'detalles' => $metodo->detalles,
               ];
           }),
       ];
   });

   // Generar el PDF usando la vista con los datos formateados
   $pdf = \PDF::loadView('reportes.pagos', compact('datosPagos'));

   // Obtener el contenido del PDF
   $contenidoPdf = $pdf->output();

   // Definir el nombre del archivo dinámicamente
   $nombreArchivo = 'Reporte_Pagos_' . now()->format('Ymd_His') . '.pdf';

   // Guardar el archivo en el directorio 'public/pdf_reports'
   $path = 'pdf_reports/' . $nombreArchivo;
   Storage::disk('public')->put($path, $contenidoPdf); // Guardar en storage/public

   // Opcional: Aquí puedes guardar información adicional en la base de datos si lo deseas

   // Devolver el archivo para descarga
   return response()->streamDownload(
       fn() => print($pdf->output()),
       $nombreArchivo,
       ['Content-Type' => 'application/pdf']
   );


    } */


    public function generarPDF(Request $request)
    {
        // Crear la consulta base
        $query = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos']);


        /*  $query = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])
          ->whereDoesntHave('facturaEmitida')  // Filtra solo los pagos sin factura emitida
          ->whereDoesntHave('reciboEmitido'); // Filtra solo los pagos sin recibo emitido*/

        // Aplicar el filtro de "carne"
        if ($request->criterio === 'carne' && $request->filled('carne')) {
            $query->whereHas('convenio.inscripcion.estudiante', function ($q) use ($request) {
                $q->where('carnet', $request->carne);
            });

            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereRaw('DATE(fecha_pago) BETWEEN ? AND ?', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            }
        }
        // Aplicar el filtro de "nombre completo"
        elseif ($request->criterio === 'nombre_completo' && $request->filled('nombre')) {
            $query->whereHas('convenio.inscripcion.estudiante.persona', function ($q) use ($request) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ['%' . $request->nombre . '%']);
            });

            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereRaw('DATE(fecha_pago) BETWEEN ? AND ?', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            }
        }
        // Si no se seleccionó un criterio, realizar búsqueda solo por fechas
        elseif ($request->criterio === 'todo') {
            // Filtrar solo por fecha sin otro criterio
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereRaw('DATE(fecha_pago) BETWEEN ? AND ?', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            }


        }

        // Obtener los pagos según la consulta con filtros
        $pagos = $query->get();

        // Formatear los datos para la vista
        $datosPagos = $pagos->map(function ($pago) {

            // Determinar el tipo de comprobante emitido
            $tipoComprobante = 'Sin Comprobante';
            if ($pago->facturaEmitida) {
                $tipoComprobante = 'Factura'." No.".$pago->facturaEmitida->serie;
            } elseif ($pago->reciboEmitido) {
                $tipoComprobante = 'Recibo'." No.".$pago->reciboEmitido->serie;
            } elseif ($pago->reciboInternoEmitido) {
                $tipoComprobante = 'Recibo Interno'." No.".$pago->reciboInternoEmitido->serie;
            }
            return [
                'id' => $pago->id,
                'convenio_id' => $pago->convenio_id,
                'monto' => $pago->monto,
                'fecha_pago' => $pago->fecha_pago,
                'tipo_comprobante' => $tipoComprobante,
                'inscripcion' => $pago->cuotas->first()->productoSeleccionado->inscripcion,
                'tipo_pago' => $pago->tipo_pago,
                'cuotas' => $pago->cuotas->map(function ($cuota) {
                    return [
                        'id' => $cuota->id,
                        'monto_cuota' => $cuota->monto_cuota,
                        'estado' => $cuota->estado,
                        'producto_seleccionado' => $cuota->productoSeleccionado ? [
                            'id' => $cuota->productoSeleccionado->id,
                            'nombre' => $cuota->productoSeleccionado->detalle->nombre ?? 'N/A',
                            'precio' => $cuota->productoSeleccionado->precio,
                        ] : null,
                    ];
                }),
                'metodos_de_pago' => $pago->pagoMetodos->map(function ($metodo) {
                    return [
                        'metodo' => $metodo->metodo_pago,
                        'monto' => $metodo->monto,
                        'detalles' => $metodo->detalles,
                    ];
                }),
            ];
        });

        $thoja = $request->input('tamano_hoja');
        $ohoja = $request->input('orientacion');

        // Generar el PDF usando la vista con los datos formateados
        $pdf = \PDF::loadView('reportes.pagos', compact('datosPagos'))
        ->setPaper($thoja, $ohoja);

        // Obtener el contenido del PDF
        $contenidoPdf = $pdf->output();

        // Definir el nombre del archivo dinámicamente
        $nombreArchivo = 'Reporte_Pagos_' . now()->format('Ymd_His') . '.pdf';

        //dd($nombreArchivo);

        // Guardar el archivo en el directorio 'public/pdf_reports'
        $path = 'pdf_reports/' . $nombreArchivo;
        Storage::disk('public')->put($path, $contenidoPdf); // Guardar en storage/public

        // Opcional: Aquí puedes guardar información adicional en la base de datos si lo deseas

        // Devolver el archivo para descarga
        return response()->streamDownload(
            fn () => print($pdf->output()),
            $nombreArchivo,
            ['Content-Type' => 'application/pdf']
        );

    }

    public function mostrarFormularioFiltros()
    {
        return view('reportes.filtrar_pagos');
    }
}
