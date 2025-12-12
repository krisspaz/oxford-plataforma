<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use XMLWriter;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function generateInvoice(Request $request)
    {
        // Datos de ejemplo (reemplazar con datos reales)
        $nitEmisor = '800000001026';
        $nombreEmisor = 'TALLER DE ZAPATERIA ALLAN ROSS';
        $nitReceptor = $request->input('nit');
        $nombreReceptor = $request->input('nombre');
        $direccionReceptor = $request->input('direccion');
        $items = $request->input('items');

        // Crear el objeto XMLWriter
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');

        // Raíz del documento
        $xml->startElement("dte:GTDocumento");
        $xml->writeAttribute("xmlns:ds", "http://www.w3.org/2000/09/xmldsig#");
        $xml->writeAttribute("xmlns:dte", "http://www.sat.gob.gt/dte/fel/0.2.0");
        // ... otros namespaces

        // Datos de emisión
        $xml->startElement("dte:SAT");
        $xml->writeAttribute("ClaseDocumento", "dte");
        $xml->startElement("dte:DTE");
        $xml->writeAttribute("ID", "DatosCertificados");
        // ... (resto de los datos de emisión)

        // Emisor
        $xml->startElement("dte:Emisor");
        $xml->writeAttribute("NITEmisor", $nitEmisor);
        $xml->writeAttribute("NombreEmisor", $nombreEmisor);
        // ... (resto de la información del emisor)
        $xml->endElement();

        // Receptor
        $xml->startElement("dte:Receptor");
        $xml->writeAttribute("IDReceptor", $nitReceptor);
        $xml->writeAttribute("NombreReceptor", $nombreReceptor);
        // ... (resto de la información del receptor)
        $xml->endElement();

        // Items
        $xml->startElement("dte:Items");
        foreach ($items as $item) {
            $xml->startElement("dte:Item");
            $xml->writeAttribute("NumeroLinea", $item['numeroLinea']);
            $xml->writeAttribute("BienOServicio", $item['bienOServicio']);
            // ... (resto de los atributos del item)
            $xml->endElement();
        }
        $xml->endElement();

        // Totales
        $xml->startElement("dte:Totales");
        // ... (cálculo de totales e inserción en el XML)
        $xml->endElement();

        // Adenda
        $xml->startElement("dte:Adenda");
        // ... (información adicional)
        $xml->endElement();

        // Cierre de elementos
        $xml->endElement(); // </dte:DTE>
        $xml->endElement(); // </dte:SAT>
        $xml->endElement(); // </dte:GTDocumento>

        // Obtener el XML generado
        $xmlString = $xml->outputMemory(true);

        // Firma digital del XML (implementar con una librería de firmas digitales)
        $xmlFirmado = firmarXML($xmlString);

        // Enviar el XML al servicio web de la SAT
        $response = Http::post('https://app.corposistemasgt.com/webservicefront/factwsfront.asmx?WSDL=null', [
            'xml' => $xmlFirmado,
            // ... otros datos
        ]);

        // Procesar la respuesta
        // ...

        return response()->json(['message' => 'Factura generada exitosamente', 'response' => $response]);
    }

    private function firmarXML($xmlString)
    {
        // Implementar la lógica para firmar el XML utilizando una librería de firmas digitales
        // ...
    }

    // ... (código del controlador existente)


    
    public function create()
    {
        return view('invoices.create');
    }
    
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nit' => 'required',
            'nombre' => 'required',
            'items' => 'required|array',
            'items.*.numeroLinea' => 'required|integer',
            'items.*.bienOServicio' => 'required',
            'items.*.cantidad' => 'required|numeric',
            'items.*.precioUnitario' => 'required|numeric',
        ]);

        // Obtener datos y calcular totales
        $nitReceptor = $request->input('nit');
        $nombreReceptor = $request->input('nombre');
        $items = $request->input('items');
        $total = $this->calcularTotal($items);

        // Generar XML
        $xmlFirmado = $this->generateXML($nitReceptor, $nombreReceptor, $items, $total);

        // Enviar al servicio web
        $response = Http::post(config('app.sat_url'), [
            'xml' => $xmlFirmado,
        ]);

        // Almacenar en base de datos (opcional)
        if ($response->successful()) {
            Invoice::create([
                'nit_receptor' => $nitReceptor,
                'nombre_receptor' => $nombreReceptor,
                'xml' => $xmlFirmado,
                'total' => $total,
            ]);
        }

        // Mostrar mensaje y redirigir
        if ($response->successful()) {
            return redirect()->route('invoices.index')->with('success', 'Factura generada exitosamente');
        } else {
            return back()->withErrors(['error' => 'Error al generar la factura: ' . $response->json('error_message')]);
        }
    }

    private function generateXML($nitReceptor, $nombreReceptor, $items, $total)
    {
        // ... código para generar el XML (similar al anterior)

        // Totales
        $xml->startElement("dte:Totales");
        $xml->writeElement("dte:Total", $total);
        // ... otros totales según la especificación del DTE
        $xml->endElement();

        // ... resto del XML

        // Firma digital
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $signedXml = new SignedXML($doc);
        // ... configuración de la firma digital
        $signedXml->sign($doc, $key);
        $xmlFirmado = $signedXml->saveXML();

        return response($xmlFirmado)
        ->header('Content-Type', 'text/xml')
        ->header('Content-Disposition', 'attachment; filename="factura.xml"');
    }

    private function calcularTotal($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['cantidad'] * $item['precioUnitario'];
        }
        return $total;
    }

    public function downloadXML(Invoice $invoice)
    {
        return response($invoice->xml)
            ->header('Content-Type', 'text/xml')
            ->header('Content-Disposition', 'attachment; filename="factura.xml"');
    }

}
