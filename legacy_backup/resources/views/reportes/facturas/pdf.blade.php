<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura SAT</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 5px;
        }
        .header .titulo {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 5px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

     <?php
        // --- Bloque PHP para cargar el Logo en Base64 desde Storage ---

        // 1. OBTENER LA RUTA RELATIVA: "uploads/2025-10/..."
        $ruta_relativa_bd = CRUDBooster::getSetting('logo_reporte');

        // 2. CONSTRUIR LA RUTA ABSOLUTA USANDO storage_path()
        // Esto apunta directamente a: /storage/app/uploads/2025-10/archivo.png
        $ruta_logo_completa = storage_path('app/' . $ruta_relativa_bd);

        $imagen_base64 = null;
        $tipo_imagen = 'png';

        // 3. VERIFICAR EXISTENCIA y GENERAR BASE64
        if (!empty($ruta_relativa_bd) && file_exists($ruta_logo_completa) && !is_dir($ruta_logo_completa)) {
            try {
                $tipo_imagen = pathinfo($ruta_logo_completa, PATHINFO_EXTENSION);
                $datos_imagen = file_get_contents($ruta_logo_completa);
                $imagen_base64 = base64_encode($datos_imagen);
            } catch (\Exception $e) {
                // Si falla la lectura, $imagen_base64 sigue siendo null.
            }
        }
    ?>

    {{-- Encabezado con logo y datos de institución --}}
   <div class="header">
    @if ($imagen_base64)
       <img src="data:image/{{ $tipo_imagen }};base64, {{ $imagen_base64 }}" alt="Logo del Reporte">
    @else
       <div style="width: 100px; height: 100px; line-height: 100px; border: 1px dashed #ccc; margin: 0 auto; text-align: center;">[LOGO NO DISPONIBLE]</div>
    @endif

    <div class="school-name">{{CRUDBooster::getSetting('nombre_del_establecimiento')}}</div>
    <div>{{CRUDBooster::getSetting('direccion_del_establecimiento')}}</div>
    <div>Teléfono: {{CRUDBooster::getSetting('numero_de_telefono')}}</div>
</div>


    {{-- Información general del primer recibo --}}
    @php
        $primerFactura = $facturas->first();
        $totalMonto = 0;
        $idsPagos = [];
    @endphp

    <div class="section-title">Datos de la Factura SAT</div>
    <table>
        <tr><th>Serie</th><td>{{ $primerFactura->serie }}</td></tr>
        <tr><th>Número</th><td>{{ $primerFactura->numero }}</td></tr>
        <tr><th>NIT</th><td>{{ $primerFactura->nit }}</td></tr>
        <tr><th>GUID</th><td>{{ $primerFactura->guid }}</td></tr>
        <tr>
            <th>Link</th>
            <td style="word-break: break-word;">
                @if($primerFactura->link)
                @php
                    $base = 'https://app.corposistemasgt.com/invoice/getdte?getinvoice=';
                    $guidCrudo = Illuminate\Support\Str::after($primerFactura->link, 'getinvoice=');
                    $linkSeguro = $base . urlencode($guidCrudo);
                @endphp
                <a href="{{ $linkSeguro }}" target="_blank" style="display: inline-block; padding: 6px 12px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
                    Descargar Factura
                </a>
            @else
                No disponible
            @endif
            </td>
        </tr>


        <tr><th>Fecha de Emisión</th><td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td></tr>
    </table>

    {{-- Procesamiento de montos y pagos --}}
    @foreach($facturas as $factura)
        @if($factura->pago)
            @php
                $idsPagos[] = $factura->pago->id;
                $totalMonto += $factura->pago->pagoMetodos->sum('monto');
            @endphp
        @endif
    @endforeach

    {{-- Información del pago agrupada --}}
    <div class="section-title">Datos del Pago (Agrupado)</div>
    <table>
        <tr>
            <th>IDs de Pago</th>
            <td>{{ implode(', ', $idsPagos) }}</td>
        </tr>
        <tr>
            <th>Monto Total</th>
            <td>Q{{ number_format($totalMonto, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Documento generado - {{ config('app.name') }}
    </div>

</body>
</html>
