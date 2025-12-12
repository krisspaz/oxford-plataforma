<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use SimpleXMLElement;
use Carbon\Carbon;
use XMLWriter;
use Exception;
use App\Models\FacturasEmitida;

class FacturaController extends Controller
{
    public function generarFactura(Request $request)
    {

        // dd($request->all());

        $validatedData = $request->validate([
            'nit' => 'required|string|max:15',
            'cliente' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'items.*.Cantidad' => 'required|numeric|min:1',
            'items.*.Descripcion' => 'required|string|max:255',
            'items.*.PrecioUnitario' => 'required|numeric|min:0',
        ]);

        // dd($request->all());


        date_default_timezone_set('America/Guatemala');

        // Fechas y variables iniciales
        $horaEmision = now()->format('Y-m-d\TH:i:s');
        $totalIVA = 0;
        $granTotal = 0;

        // Datos del receptor y emisor (se pueden reemplazar por datos dinámicos)
        $nit = $request->input('nit', '');
        $cliente = $request->input('cliente', '');
        $direccion = $request->input('direccion', '');
        $estudiante = $request->input('estudiante', '');
        $carnet = $request->input('carnet', '');
        $codigo_familiar = $request->input('codigo_familiar', '');
        $nombre_familiar = $request->input('nombre_familiar', '');
        $resolucion = $request->input('resolucion', '');
        $metodo_pago = $request->input('Metodo_Pago', '');
        $monto = $request->input('Monto', '');
        $documentos = $request->input('Documentos', '');
        $bancos = $request->input('Bancos', '');
        $items = $request->input('items');
        $TipoEspecial = $request->input('tipoespecial');



        // Crear XMLWriter
        $w = new XMLWriter();
        $w->openMemory();
        $w->startDocument('1.0', 'UTF-8');

        // Raíz del documento
        $w->startElement("dte:GTDocumento");
        $w->writeAttribute("xmlns:cesp", "http://www.sat.gob.gt/face2/ComplementoEspectaculos/0.1.0");
        $w->writeAttribute("xmlns:cfc", "http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0");
        $w->writeAttribute("xmlns:cno", "http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0");
        $w->writeAttribute("xmlns:ds", "http://www.w3.org/2000/09/xmldsig#");
        $w->writeAttribute("xmlns:crc", "http://www.sat.gob.gt/face2/ComplementoReferenciaConstancia/0.1.0");
        $w->writeAttribute("xmlns:cex", "http://www.sat.gob.gt/face2/ComplementoExportaciones/0.1.0");
        $w->writeAttribute("xmlns:cfe", "http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0");
        $w->writeAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $w->writeAttribute("xmlns:cca", "http://www.sat.gob.gt/face2/CobroXCuentaAjena/0.1.0");
        $w->writeAttribute("xmlns:dte", "http://www.sat.gob.gt/dte/fel/0.2.0");
        $w->writeAttribute("Version", "0.1");

        // SAT y DTE
        $w->startElement("dte:SAT");
        $w->writeAttribute("ClaseDocumento", "dte");
        $w->startElement("dte:DTE");
        $w->writeAttribute("ID", "DatosCertificados");

        // Datos de Emisión
        $w->startElement("dte:DatosEmision");
        $w->writeAttribute("ID", "DatosEmision");
        $this->agregarDatosGenerales($w, $horaEmision);
        $this->agregarEmisor($w);
        $this->agregarReceptor($w, $nit, $cliente, $direccion, $TipoEspecial);
        $this->agregarItems($w, $items, $totalIVA, $granTotal);
        $this->agregarTotales($w, $totalIVA, $granTotal);
        $w->endElement(); // Fin DatosEmision
        $w->endElement(); // Fin DTE
        $this->agregarAdenda($w, $estudiante, $carnet, $codigo_familiar, $nombre_familiar, $resolucion, $metodo_pago, $monto, $documentos, $bancos);



        $w->endElement(); // Fin SAT

        $w->endElement(); // Fin GTDocumento

        // Generar XML y retornar como respuesta

        $var = $w->outputMemory(true);


        $xml = base64_encode($var);


        // dd($request->pago_id);
        // Crear un array con todos los valores

        if ($request->pago_id) {

            $facturaExistente = FacturasEmitida::where('pago_id', $request->pago_id)->first();

            if ($facturaExistente) {

                // Si ya hay un recibo emitido con certificado FEL
                if (!empty($facturaExistente->link)) {
                    return response()->streamDownload(function () use ($facturaExistente) {
                        echo file_get_contents($facturaExistente->link);
                    }, "FACTURA-{$facturaExistente->numero}.pdf");
                }

                // Si existe registro pero falló certificación
                return redirect()->back()->with('error', 'Este pago ya tiene un recibo registrado, pero no se obtuvo un enlace de certificación.');
            }
        }



        $response = $this->enviarSolicitudSOAP($xml, $request->pago_id, $nit);

        if (!$response || !$response['resultado']) {
            return redirect()->back()->with('error', $response['descripcion'] ?? 'Error desconocido al procesar la solicitud');
        }

        return redirect($response['link'])
                ->with('success', 'Documento generado correctamente')
                ->with('descripcion', $response['descripcion'])
                ->with('fecha', $response['fecha']);



        //return view('factura.exito');
    }

    private function agregarDatosGenerales($w, $horaEmision)
    {
        $w->startElement("dte:DatosGenerales");
        $w->writeAttribute("CodigoMoneda", env('SOAP_CODIGO_MONEDA'));
        $w->writeAttribute("FechaHoraEmision", $horaEmision);
        $w->writeAttribute("Tipo", "FACT");
        $w->endElement();
    }

    private function agregarEmisor($w)
    {
        $w->startElement("dte:Emisor");
        $w->writeAttribute("AfiliacionIVA", env('SOAP_AFILIACION_IVA'));
        $w->writeAttribute("CodigoEstablecimiento", env('SOAP_ESTABLECIMIENTO_FACT'));
        $w->writeAttribute("NITEmisor", env('SOAP_EMISOR_NIT'));
        $w->writeAttribute("NombreComercial", env('SOAP_NOMBRE_COMERCIAL'));
        $w->writeAttribute("NombreEmisor", env('SOAP_NOMBRE_EMISOR'));

        $w->startElement("dte:DireccionEmisor");
        $w->writeElement("dte:Direccion", env('SOAP_DIRECCION_COMERCIAL'));
        $w->writeElement("dte:CodigoPostal", env('SOAP_CODIGO_POSTAL_COMERCIAL'));
        $w->writeElement("dte:Municipio", env('SOAP_MUNICIPIO_COMERCIAL'));
        $w->writeElement("dte:Departamento", env('SOAP_DEPARTAMENTO_COMERCIAL'));
        $w->writeElement("dte:Pais", env('SOAP_PAIS_COMERCIAL'));
        $w->endElement(); // Fin DireccionEmisor
        $w->endElement(); // Fin Emisor
    }

    private function agregarReceptor($w, $nit, $cliente, $direccion, $TipoEspecial)
    {
        $w->startElement("dte:Receptor");
        $w->writeAttribute("CorreoReceptor", "");
        $w->writeAttribute("IDReceptor", $nit);
        $w->writeAttribute("NombreReceptor", $cliente);

        if ($TipoEspecial!="NIT") {
            $w->writeAttribute("TipoEspecial", $TipoEspecial);
        }


        $w->startElement("dte:DireccionReceptor");
        $w->writeElement("dte:Direccion", $direccion);
        $w->writeElement("dte:CodigoPostal", "03012");
        $w->writeElement("dte:Municipio", "COBAN");
        $w->writeElement("dte:Departamento", "ALTA VERAPAZ");

        $w->writeElement("dte:Pais", "GT");
        $w->endElement(); // Fin DireccionReceptor
        $w->endElement(); // Fin Receptor
    }

    private function agregarItems($w, $items, &$totalIVA, &$granTotal)
    {
        // Obtener el contenido de 'items' como una cadena JSON


        /*  $itemsJson = $items; // Default es un array vacío si no se pasa 'items'

          // Decodificar el JSON a un array en PHP
          $items = json_decode($itemsJson, true); // Decodificación como array asociativo
         // dd($items);
          // Verificar si la decodificación fue exitosa
          if (json_last_error() !== JSON_ERROR_NONE) {
              return response()->json(['error' => 'Formato de JSON inválido'], 400);
          } */

        $w->startElement("dte:Frases");

        $w->startElement("dte:Frase");
        $w->writeAttribute("CodigoEscenario", env('SOAP_CODIGO_ESCENARIO_FACT'));
        $w->writeAttribute("TipoFrase", env('SOAP_CODIGO_FRASE_FACT'));

        $w->endElement(); // FiN Frase


        $w->endElement(); // FiN Frases

        $w->startElement("dte:Items");






        foreach ($items as $item) {
            $w->startElement("dte:Item");
            $w->writeAttribute("BienOServicio", $item['BienOServicio']);

            $w->writeAttribute("NumeroLinea", $item['NumeroLinea']);
            $w->writeElement("dte:Cantidad", $item['Cantidad']);
            $w->writeElement("dte:UnidadMedida", $item['UnidadMedida']);
            $w->writeElement("dte:Descripcion", $item['Descripcion']);
            $w->writeElement("dte:PrecioUnitario", $item['PrecioUnitario']);
            $w->writeElement("dte:Precio", $item['Precio']);
            $w->writeElement("dte:Descuento", $item['Descuento']);

            $w->startElement("dte:Impuestos");
            $w->startElement("dte:Impuesto");
            $w->writeElement("dte:NombreCorto", "IVA");
            $w->writeElement("dte:CodigoUnidadGravable", "1");
            $w->writeElement("dte:MontoGravable", $item['MontoGravable']);
            $w->writeElement("dte:MontoImpuesto", $item['MontoImpuesto']);
            $w->endElement(); // Fin Impuesto
            $w->endElement(); // Fin Impuestos

            $w->writeElement("dte:Total", $item['Total']);
            $w->endElement(); // Fin Item



            $totalIVA += $item['MontoImpuesto'];
            $granTotal += $item['Total'];
        }

        $w->endElement(); // Fin Items
    }

    private function agregarTotales($w, $totalIVA, $granTotal)
    {
        $w->startElement("dte:Totales");
        $w->startElement("dte:TotalImpuestos");
        $w->startElement("dte:TotalImpuesto");
        $w->writeAttribute("NombreCorto", "IVA");
        $w->writeAttribute("TotalMontoImpuesto", number_format($totalIVA, 2, '.', ''));
        $w->endElement(); // Fin TotalImpuesto
        $w->endElement(); // Fin TotalImpuestos
        $w->writeElement("dte:GranTotal", number_format($granTotal, 2, '.', ''));
        $w->endElement(); // Fin Totales
    }

    private function agregarAdenda($w, $estudiante, $carnet, $codigo_familiar, $nombre_familiar, $resolucion, $metodo_pago, $monto, $documentos, $bancos)
    {

        //dd( $granTotal);
        $w->startElement("dte:Adenda");
        $w->startElement("CorreoElectronico");
        $w->writeElement("Asunto", "Envio de documento electrónico");
        $w->writeElement("De", "postmaster@corposistemasgt.com");
        $w->writeElement("Para", "");
        $w->writeElement("Adjuntos", "PDF");
        $w->endElement(); // Fin CorreoElectronico

        $w->startElement("DetallesAdicional");
        $w->startElement("DetalleAdicional");


        $w->writeElement("Valor1", "");
        $w->writeElement("Valor2", "");
        $w->writeElement("Valor3", "");
        $w->writeElement("Valor4", "");
        $w->writeElement("Valor5", "");
        $w->writeElement("Valor6", $estudiante);
        $w->writeElement("Valor7", "");
        $w->writeElement("Valor8", "");
        $w->writeElement("Valor9", $codigo_familiar);
        $w->writeElement("Valor10", $nombre_familiar);
        $w->writeElement("Valor11", $carnet);
        $w->writeElement("Valor12", "");
        $w->writeElement("Valor13", "");
        $w->writeElement("Valor14", "");
        $w->writeElement("Valor15", $resolucion);
        $w->writeElement("Valor16", "");
        $w->writeElement("Valor17", $metodo_pago);
        $w->writeElement("Valor18", $monto);
        $w->writeElement("Valor19", $documentos);
        $w->writeElement("Valor20", $bancos);

        $w->endElement(); // Fin Detalle Adicional
        $w->endElement(); // Fin DetallesAdicionales



        $w->endElement(); // Fin Adenda
    }

    public function enviarSolicitudSOAP($xml, $pagoIds, $nit)
    {
        try {
            if (!is_array($pagoIds)) {
                throw new Exception('El parámetro $pagoIds debe ser un array.');
            }

            // 🕒 Fecha actual Guatemala
            $fecha = Carbon::now('America/Guatemala')->format('Y-m-d\TH:i:s');
            $Data3 = "";

            // 🔐 Datos fijos (sin .env como pediste)

            $Requestor = env('REQUESTOR');
            $Entity = env('ENTITY');
            $UserName = env('SOAP_USERNAM');
            $Data1 = env('SOAP_DATA1');
            $Transaction = env('SOAP_TRANSACTION');
            $Country = env('SOAP_COUNTRY');
            $SoapUrl = env('SOAP_URL');

            // 📌 Construcción del XML SOAP
            $soapBody = '<?xml version="1.0" encoding="UTF-8"?>
            <SOAP-ENV:Envelope xmlns:ws="http://www.fact.com.mx/schema/ws" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                <SOAP-ENV:Header/>
                <SOAP-ENV:Body>
                    <ws:RequestTransaction>
                        <ws:Requestor>' . $Requestor . '</ws:Requestor>
                        <ws:Transaction>' . $Transaction . '</ws:Transaction>
                        <ws:Country>' . $Country . '</ws:Country>
                        <ws:Entity>' . $Entity . '</ws:Entity>
                        <ws:User>' . $Requestor . '</ws:User>
                        <ws:UserName>' . $UserName . '</ws:UserName>
                        <ws:Data1>' . $Data1 . '</ws:Data1>
                        <ws:Data2>' . $xml . '</ws:Data2>
                        <ws:Data3>' . $Data3 . '</ws:Data3>
                    </ws:RequestTransaction>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

            // 🔌 CURL request
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $SoapUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $soapBody,
                CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            if (!$response) {
                throw new Exception("No se obtuvo respuesta del servidor SOAP.");
            }

            // 🛠 Limpieza del XML para poder parsearlo
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xmlResponse = new SimpleXMLElement($response);
            $body = $xmlResponse->xpath('//soapBody')[0];
            $data = json_decode(json_encode($body), true);

            $resultadoData = $data['RequestTransactionResponse']['RequestTransactionResult']['Response'];

            $resultado = $resultadoData['Result'] ?? 'false';
            $descripcion = $resultadoData['Description'] ?? 'Sin descripción';
            $timestamp = $resultadoData['TimeStamp'] ?? null;

            if ($resultado !== "true") {
                return [
                    'resultado' => false,
                    'descripcion' => $descripcion,
                    'fecha' => $timestamp,
                    'link' => null
                ];
            }

            // ✅ Si fue exitoso, obtener datos del SAT
            $identificador = $resultadoData['Identifier'] ?? [];
            $guid = $identificador['DocumentGUID'] ?? null;
            $serie = $identificador['Batch'] ?? null;
            $numero = $identificador['Serial'] ?? null;
            $link = $identificador['InternalID'] ?? null;

            // 💾 Guardar recibos en BD por cada pago
            foreach ($pagoIds as $pago) {
                FacturasEmitida::create([
                    'pago_id' => $pago ?: null,
                    'nit' => $nit,
                    'guid' => $guid,
                    'serie' => $serie,
                    'numero' => $numero,
                    'link' => $link,
                ]);
            }

            return [
                'resultado' => true,
                'descripcion' => $descripcion,
                'fecha' => $timestamp,
                'link' => $link,
                'serie' => $serie,
                'numero' => $numero,
                'guid' => $guid,
            ];

        } catch (Exception $e) {
            return [
                'resultado' => false,
                'descripcion' => $e->getMessage(),
                'fecha' => null,
                'link' => null,
            ];
        }
    }

    public function descargarFactura($id)
    {
        // Buscar la factura en la base de datos
        $factura = FacturasEmitida::findOrFail($id);

        // Preparar la descarga del archivo o redirigir al enlace
        return redirect($factura->link);
    }

    public function consultarNit($nit)
    {
        try {
            // URL de la API
            $apiUrl = "https://corpo-sistemas.com/corpoconnect/corpo/consultanit.php?nit={$nit}";

            // Hacer la solicitud a la API externa
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if ($nit == "CF" || $nit == "cf") {
                return response()->json([
                    'success' => true,
                    'nombre' => "CONSUMIDOR FINAL"
                ]);

            } else {

                // Validar la respuesta
                if (isset($data['resultado']) && $data['resultado'] === 'true') {
                    return response()->json([
                        'success' => true,
                        'nombre' => $data['nombre']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'NIT no encontrado'
                    ]);
                }

            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el NIT'
            ]);
        }
    }


    public function download($id)
    {
        $factura = FacturasEmitida::findOrFail($id);

        // o el path que usesfa
        return response()->download($factura->link);
    }





}
