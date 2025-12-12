<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecibInternooEmitido;
use App\Models\ReciboInternooEmitidoBitacora;

class ReporteRecibosInternosController extends Controller
{
    //

    public function index()
    {
        $recibos = RecibInternooEmitido::with('pago')->get();
        $recibosanulados = ReciboInternooEmitidoBitacora::with('pago', 'cmsUser')->get();



        return view('reportes.recibosinternos.index', compact('recibos', 'recibosanulados'));
    }

    public function recibosAnuladas(Request $request)
    {
        // Número de registros por página (default 10)
        $perPage = $request->input('per_page', 10);
        $recibos = RecibInternooEmitido::where('anular', 1)->paginate($perPage)->appends($request->query()); // solo las anuladas

        return view('reportes.recibosinternos.anuladas', compact('recibos'));
    }

    public function show($serie)
    {
        $recibos = RecibInternooEmitido::with('pago')
    ->where('serie', $serie)
    ->get();

        //dd($recibos);
        return view('reportes.recibosinternos.show', compact('recibos'));
    }

    public function buscarPorNit(Request $request)
    {
        $request->validate([
              'nit' => 'required|string',
          ]);

        // Facturas activas
        $recibos  = RecibInternooEmitido::where('nit', $request->nit)
            ->with('pago')
            ->get();

        // Facturas anuladas
        $recibosanulados = ReciboInternooEmitidoBitacora::where('nit', $request->nit)
           ->with('pago', 'cmsUser')
           ->get();



        return view('reportes.recibosinternos.index', compact('recibos', 'recibosanulados'));
    }

    public function descargarPDF($serie)
    {

        $recibos = RecibInternooEmitido::with('pago')
        ->where('serie', $serie)
        ->get();




        $pdf = \PDF::loadView('reportes.recibosinternos.pdf', compact('recibos'));

        return $pdf->download('recibo_'.$recibos->first()->serie.'.pdf');
    }

    public function anular(Request $request, $numero)
    {


        // Buscar todas las facturas que tengan el mismo número
        $recibos = RecibInternooEmitido::where('numero', $numero)->get();

        // Verificar si existen facturas con ese número
        if ($recibos->isEmpty()) {
            return redirect()->route('reportes.recibosinternos.index')->with('error', 'No se encontraron recibos con ese número.');
        }

        // Actualizar el campo anular y motivo en todas las facturas encontradas
        foreach ($recibos as $recibo) {
            if ($request->input('anular')==1) {
                $recibo->anular = $request->input('anular');
                $recibo->motivo = $request->input('motivo');
                $recibo->save();
            } elseif ($request->input('anular')==2) {
            } else {
                $recibo->anular = null;
                $recibo->motivo = null;
                $recibo->save();
            }

        }

        return back()->with('success', 'Solicitud Enviada Correctamente.');
    }
}
