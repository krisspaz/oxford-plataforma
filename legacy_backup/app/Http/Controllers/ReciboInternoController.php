<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd; // Usamos Imagick en lugar de GD
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Barryvdh\DomPDF\Facade as Pdf;
use Illuminate\Support\Str;
use App\Models\RecibInternooEmitido;

class ReciboInternoController extends Controller
{
    public function mostrarFormulario()
    {
        return view('recibos.detalle');
    }

    public function generarRecibo(Request $request)
    {

        //  dd( $request->all());
        // Validación y preparación (igual)
        $validatedData = $request->validate([
          'nit' => 'required|string|max:15',
          'cliente' => 'required|string|max:255',
          'direccion' => 'required|string|max:255',

          'items.*.Cantidad' => 'required|numeric|min:1',
          'items.*.Descripcion' => 'required|string|max:255',
          'items.*.PrecioUnitario' => 'required|numeric|min:0',
    ]);

        $total = 0;
        $items = [];
        foreach ($request->input('items') as $item) {
            $item['valor'] = $item['Cantidad'] * $item['PrecioUnitario'];
            $total += $item['valor'];
            $items[] = $item;
        }

        // QR
        $renderer = new ImageRenderer(new RendererStyle(200), new ImagickImageBackEnd());
        $writer = new Writer($renderer);
        $qr_image = base64_encode($writer->writeString("Recibo generado para {$validatedData['cliente']} con la cantidad de Q.{$total}"));

        $numeroAutorizacion = Str::uuid()->toString();
        $numeroSerie = $this->generarNumeroSerie();
        $ultimoRecibo = RecibInternooEmitido::latest('id')->first();
        $numeroCorrelativo = $ultimoRecibo ? $ultimoRecibo->numero + 1 : 1;

        $data = [
            'numero_recibo' => $numeroCorrelativo,
            'cliente' => $validatedData['cliente'],
            'direccion' => $validatedData['direccion'],
            'nit' => $validatedData['nit'],
            'items' => $items,
            'total' => $total,
            'fecha_certificacion' => now()->format('d/m/Y'),
            'numero_autorizacion' => $numeroAutorizacion,
            'qr_code' => $qr_image,
            'numero_serie' => $numeroSerie,
            'carnet' => $request->carnet,
            'alumno' => $request->estudiante,
            'familia' => $request->codigo_familiar,
           'metodo_pago' => $request->Metodo_Pago,
        ];

        // ✅ Ruta del PDF en storage/app/public/recibos
        $pdfPath = 'recibos/recibo_' . $numeroAutorizacion . '.pdf';

        // ✅ Crear carpeta si no existe
        Storage::disk('public')->makeDirectory('recibos');

        // ✅ Guardar el PDF en storage/app/public
        $pdf = Pdf::loadView('recibos.recibo', $data)->setPaper([0, 0, 180, 700], 'portrait');
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // ✅ Guardar en base de datos
        if ($request->pago_id != null) {
            foreach ($request->pago_id as $pago) {
                RecibInternooEmitido::create([
                    'pago_id' => $pago,
                    'nit' => $validatedData['nit'],
                    'guid' => $numeroAutorizacion,
                    'serie' => $numeroSerie,
                    'numero' => $numeroCorrelativo,
                    'link' => $pdfPath, // Solo 'recibos/archivo.pdf'
                     'anular' => null,
                      'motivo' => null,
                ]);
            }
        } else {
            RecibInternooEmitido::create([
                'pago_id' => null,
                'nit' => $validatedData['nit'],
                'guid' => $numeroAutorizacion,
                'serie' => $numeroSerie,
                'numero' => $numeroCorrelativo,
                'link' => $pdfPath,
                 'anular' => null,
                      'motivo' => null,
            ]);
        }

        // ✅ Redirigir o mostrar éxito
        return response()->download(storage_path("app/public/" . $pdfPath))->deleteFileAfterSend(false);
    }






    public function mostrarExito()
    {
        return view('recibos.exito');
    }

    public function descargarRecibo(Request $request)
    {
        $path = $request->query('path');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return back()->withErrors(['error' => 'El archivo no existe.']);
    }

    private function generarNumeroSerie()
    {
        $letras1 = strtoupper(chr(rand(65, 90)));
        $letras2 = strtoupper(chr(rand(65, 90)));
        $numero1 = str_pad(rand(0, 99), 3, "0", STR_PAD_LEFT);
        $letra3 = strtoupper(chr(rand(65, 90)));
        $numero2 = str_pad(rand(0, 99), 2, "0", STR_PAD_LEFT);

        return $letras1 . $letras2 . $numero1 . $letra3 . $numero2;
    }



    public function download($id)
    {


        $recibointerno = RecibInternooEmitido::findOrFail($id);

        dd($recibointerno);


        return response()->download($recibointerno);
    }


}
