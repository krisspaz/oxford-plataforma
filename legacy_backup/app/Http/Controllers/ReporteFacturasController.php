<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacturasEmitida;
use App\Models\FacturaEmitidaBitacora;

class ReporteFacturasController extends Controller
{
    //

    public function index()
    {
        $facturas = FacturasEmitida::with('pago')->get();
        $facturasanuladas = FacturaEmitidaBitacora::with('pago', 'cmsUser')->get();


        return view('reportes.facturas.index', compact('facturas', 'facturasanuladas'));
    }

    public function facturasAnuladas(Request $request)
    {
        // Número de registros por página (default 10)
        $perPage = $request->input('per_page', 10);
        $facturas = FacturasEmitida::where('anular', 1)->paginate($perPage)->appends($request->query()); // solo las anuladas

        return view('reportes.facturas.anuladas', compact('facturas'));
    }

    public function show($serie)
    {


        $facturas = FacturasEmitida::with('pago')
        ->where('serie', $serie)
        ->get();
        return view('reportes.facturas.show', compact('facturas'));
    }

    public function buscarPorNit(Request $request)
    {
        $request->validate([
        'nit' => 'required|string',
            ]);

        // Facturas activas
        $facturas = FacturasEmitida::where('nit', $request->nit)
            ->with('pago')
            ->get();

        // Facturas anuladas
        $facturasanuladas = FacturaEmitidaBitacora::where('nit', $request->nit)
            ->with('pago', 'cmsUser')
            ->get();

        return view('reportes.facturas.index', compact('facturas', 'facturasanuladas'));
    }

    public function descargarPDF($serie)
    {


        $facturas = FacturasEmitida::with('pago')
        ->where('serie', $serie)
        ->get();


        $pdf = \PDF::loadView('reportes.facturas.pdf', compact('facturas'));

        return $pdf->download('factura_'.$facturas->first()->serie.'.pdf');
    }

    public function anular(Request $request, $numero)
    {
        // Buscar todas las facturas que tengan el mismo número
        $facturas = FacturasEmitida::where('numero', $numero)->get();

        // Verificar si existen facturas con ese número
        if ($facturas->isEmpty()) {
            return redirect()->route('reportes.facturas.index')->with('error', 'No se encontraron facturas con ese número.');
        }

        // Actualizar el campo anular y motivo en todas las facturas encontradas
        foreach ($facturas as $factura) {
            if ($request->input('anular')==1) {
                $factura->anular = $request->input('anular');
                $factura->motivo = $request->input('motivo');
                $factura->save();
            } elseif ($request->input('anular')==2) {
            } else {
                $factura->anular = null;
                $factura->motivo = null;
                $factura->save();
            }
        }

        return back()->with('success', 'Solicitud Enviada Correctamente.');
    }







}
