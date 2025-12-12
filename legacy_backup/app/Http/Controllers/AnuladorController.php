<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FacturasEmitida;
use App\Models\FacturaEmitidaBitacora;
use App\Models\ReciboEmitidoSatBitacora;
use App\Models\ReciboInternooEmitidoBitacora;
use App\Models\ReciboEmitido;
use App\Models\RecibInternooEmitido;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Carbon\Carbon;

class AnuladorController extends Controller
{

    // ... (otras funciones del controlador) ...

    public function generarXmlAnulacionBase64(
        string $numeroDocumentoAAnular,
        string $nitEmisor,
        string $idReceptor,
        string $fechaEmisionDocumentoAnular,
        string $motivoAnulacion
    ): ?string {
        try {
            $fechaHoraAnulacion = now()->format('Y-m-d\TH:i:s'); // Genera la fecha y hora actual

            $xml = <<<XML
    <dte:GTAnulacionDocumento
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Version="0.1"
        xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0">
        <dte:SAT>
            <dte:AnulacionDTE ID="DatosCertificados">
                <dte:DatosGenerales ID="DatosAnulacion"
                    NumeroDocumentoAAnular="{$numeroDocumentoAAnular}"
                    NITEmisor="{$nitEmisor}"
                    IDReceptor="{$idReceptor}"
                    FechaEmisionDocumentoAnular="{$fechaEmisionDocumentoAnular}"
                    FechaHoraAnulacion="{$fechaHoraAnulacion}"
                    MotivoAnulacion="{$motivoAnulacion}"/>
            </dte:AnulacionDTE>
        </dte:SAT>
    </dte:GTAnulacionDocumento>
    XML;
            // Codifica el XML a Base64
            $xmlBase64 = base64_encode($xml);
            return $xmlBase64;
        } catch (\Exception $e) {
            Log::error('Error al generar XML de anulación:', ['error' => $e->getMessage()]);
            return null;
        }
    }
    // ... (tu función anularDocumento, modificada para usar esta función) ...
    public function anularDocumento(Request $request)
    {
        $numeroDocumento = $request->input('numero_documento');
        $nitEmisor = env('ENTITY');
        $idReceptor = $request->input('nit_receptor', 'CF');
        $fechaEmision = Carbon::parse($request->input('fecha_emision'))->format('Y-m-d\TH:i:s');
        $motivoAnulacion = $request->input('motivo');
        $serie = $request->input('numero_serie');

        if (!$numeroDocumento || !$fechaEmision || !$motivoAnulacion) {
            return back()->with('error', 'Faltan parámetros requeridos para la anulación');
        }

        $xmlBase64 = $this->generarXmlAnulacionBase64(
            $numeroDocumento,
            $nitEmisor,
            $idReceptor,
            $fechaEmision,
            $motivoAnulacion
        );

        if (!$xmlBase64) {
            return back()->with('error', 'No fue posible generar el XML de anulación.');
        }

        // === Config SOAP ===
        $Requestor = env('REQUESTOR');
        $SoaUrl = env('SOAP_URL');
        $SoaTransaction = env('SOAP_TRANSACTION', 'SYSTEM_REQUEST');
        $SoaCountry = env('SOAP_COUNTRY');
        $Entity = env('ENTITY');
        $UserName = env('SOAP_USERNAME');
        $Data1 = env('SOAPANULAR_DATA1');

        $soapBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:ws="http://www.fact.com.mx/schema/ws" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Header/>
    <SOAP-ENV:Body>
        <ws:RequestTransaction>
            <ws:Requestor>{$Requestor}</ws:Requestor>
            <ws:Transaction>{$SoaTransaction}</ws:Transaction>
            <ws:Country>{$SoaCountry}</ws:Country>
            <ws:Entity>{$Entity}</ws:Entity>
            <ws:User>{$Requestor}</ws:User>
            <ws:UserName>{$UserName}</ws:UserName>
            <ws:Data1>{$Data1}</ws:Data1>
            <ws:Data2>{$xmlBase64}</ws:Data2>
            <ws:Data3></ws:Data3>
        </ws:RequestTransaction>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

        // === Envío SOAP ===
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $SoaUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soapBody,
            CURLOPT_HTTPHEADER => ['Content-Type: text/xml'],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return back()->with('error', "Error al conectar con certificador: $error");
        }

        Log::info("Respuesta de anulación SOAP", ['response' => $response]);
        // === Limpieza XML namespaces para SimpleXML ===
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        try {
            $xml = simplexml_load_string($response);
            $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xml->registerXPathNamespace('ws', 'http://www.fact.com.mx/schema/ws');

            // Buscar valores usando XPath para evitar errores de namespace
            $resultNode = $xml->xpath('//ws:Response/ws:Result');
            $descriptionNode = $xml->xpath('//ws:Response/ws:Description');
            $codeNode = $xml->xpath('//ws:Response/ws:Code');

            $result = $resultNode ? (string) $resultNode[0] : null;
            $description = $descriptionNode ? (string) $descriptionNode[0] : 'Sin descripción';
            $code = $codeNode ? (string) $codeNode[0] : 'Sin código';

            if ($result === "true") {
                $this->procesaranulacion($serie);
                return back()->with('success', "Documento anulado exitosamente: $description");
            } else {
                return back()->with('error', "Error $code: $description");
            }

        } catch (\Exception $e) {
            Log::error("Error procesando respuesta SOAP", ['exception' => $e]);
            return back()->with('error', 'El certificador devolvió una respuesta no válida');
        }

    }


    public function procesaranulacion($serie)
    {
        $facturas = FacturasEmitida::where('serie', $serie)->get();
        $recibosat = ReciboEmitido::where('serie', $serie)->get();
        $recibointerno = RecibInternooEmitido::where('serie', $serie)->get();
        // Anular Facturas
        if (!$facturas->isEmpty()) {
            foreach ($facturas as $factura) {
                FacturaEmitidaBitacora::create([
                    'pago_id' => $factura->pago_id,
                    'nit' => $factura->nit,
                    'guid' => $factura->guid,
                    'serie' => $factura->serie,
                    'numero' => $factura->numero,
                    'link' => $factura->link,
                    'anular' => $factura->anular,
                    'motivo' => $factura->motivo,
                    'anulada_en' => now(),
                    'cms_users_id' => CRUDBooster::myId(),
                ]);
                $factura->delete();
            }
        }
        // Anular Recibos SAT
        if (!$recibosat->isEmpty()) {
            foreach ($recibosat as $recibo) {
                ReciboEmitidoSatBitacora::create([
                    'pago_id' => $recibo->pago_id,
                    'nit' => $recibo->nit,
                    'guid' => $recibo->guid,
                    'serie' => $recibo->serie,
                    'numero' => $recibo->numero,
                    'link' => $recibo->link,
                    'anular' => $recibo->anular,
                    'motivo' => $recibo->motivo,
                    'anulada_en' => now(),
                    'cms_users_id' => CRUDBooster::myId(),
                    'created_at' => $recibo->created_at,
                    'updated_at' => $recibo->updated_at,
                ]);

                $recibo->delete();
            }
        }
        // Anular Recibo Interno
        if (!$recibointerno->isEmpty()) {
            foreach ($recibointerno as $reciboi) {
                ReciboInternooEmitidoBitacora::create([
                    'pago_id' => $reciboi->pago_id,
                    'nit' => $reciboi->nit,
                    'guid' => $reciboi->guid,
                    'serie' => $reciboi->serie,
                    'numero' => $reciboi->numero,
                    'link' => $reciboi->link,
                    'anular' => $reciboi->anular,
                    'motivo' => $reciboi->motivo,
                    'anulada_en' => now(),
                    'cms_users_id' => CRUDBooster::myId(),
                    'created_at' => $reciboi->created_at,
                    'updated_at' => $reciboi->updated_at,
                ]);

                $reciboi->delete();
            }
        }

        return redirect()->back()->with('success', 'Proceso de anulación completado.');

    }




}
