<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReciboEmitido;
use App\Models\ReciboEmitidoSatBitacora;
use App\Models\CmsNotification;
use CRUDBooster;

class ReporteRecibosSatController extends Controller
{
    //

    public function index()
    {
        $recibos = ReciboEmitido::with('pago')->get();
        $recibosanulados = ReciboEmitidoSatBitacora::with('pago', 'cmsUser')->get();


        return view('reportes.recibossat.index', compact('recibos', 'recibosanulados'));
    }

    public function recibosAnuladas(Request $request)
    {
        // Número de registros por página (default 10)
        $perPage = $request->input('per_page', 10);
        $recibos = ReciboEmitido::where('anular', 1)->paginate($perPage)->appends($request->query());  // solo las anuladas

        return view('reportes.recibossat.anuladas', compact('recibos'));
    }

    public function show($serie)
    {
        $recibos = ReciboEmitido::with('pago')
        ->where('serie', $serie)
        ->get();
        return view('reportes.recibossat.show', compact('recibos'));
    }

    public function buscarPorNit(Request $request)
    {
        $request->validate([
              'nit' => 'required|string',
          ]);

        //Recibos activas
        $recibos = ReciboEmitido::where('nit', $request->nit)
            ->with('pago')
            ->get();

        // Recibos anuladas
        $recibosanulados = ReciboEmitidoSatBitacora::where('nit', $request->nit)
            ->with('pago', 'cmsUser')
            ->get();



        return view('reportes.recibossat.index', compact('recibos', 'recibosanulados'));
    }

    public function descargarPDF($serie)
    {
        $recibos = ReciboEmitido::with('pago')
        ->where('serie', $serie)
        ->get();



        $pdf = \PDF::loadView('reportes.recibossat.pdf', compact('recibos'));

        return $pdf->download('recibo_'.$recibos->first()->serie.'.pdf');
    }

    public function anular(Request $request, $numero)
    {
        // Buscar todas las facturas que tengan el mismo número
        $recibos = ReciboEmitido::where('numero', $numero)->get();

        // Verificar si existen facturas con ese número
        if ($recibos->isEmpty()) {
            return redirect()->route('reportes.recibos.index')->with('error', 'No se encontraron recibos con ese número.');
        }

        /* Implementar en el futuro
                if($request->input('anular')==1){
                    $mensaje = "Solicitud de Anulación Recibo SAT '{$recibo->serie}'";
                     $urlNotificacion = route('reportes.recibos.anuladas');
                        CmsNotification::create([
                            'content'      => $mensaje,
                             'id_cms_users' => 2,
                            'url'          => $urlNotificacion,
                            'is_read'      => 0,
                        ]);
                }else if($request->input('anular')==0){
                    $mensaje = "Solicitud de Anulación Recibo SAT Rechazada'{$recibo->serie}'";
                     $urlNotificacion = route('reportes.recibos.index');
                        CmsNotification::create([
                            'content'      => $mensaje,
                             'id_cms_users' => CRUDBooster::myId(),
                            'url'          => $urlNotificacion,
                            'is_read'      => 0,
                        ]);
                }
                */

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
