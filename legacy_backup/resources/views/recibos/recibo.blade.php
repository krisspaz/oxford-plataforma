<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante Interno</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .ticket-container {
            width: 180px; /* 58mm = aprox 180 puntos */
            margin: 0 auto;
            padding: 5px;
            font-size: 12px;

            box-sizing: border-box;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .qr-code {
            width: 80px;
            height: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 2px 0;
            text-align: left;
            word-break: break-word;
        }

        .border-top {
            border-top: 1px dashed black;
            margin: 4px 0;
        }

        img.logo {
            width: 50px;
            height: auto;
        }
    </style>
</head>
<body>

      <?php
        // --- Bloque PHP para cargar el Logo en Base64 desde Storage ---

        // 1. OBTENER LA RUTA RELATIVA: "uploads/2025-10/..."
        $ruta_relativa_bd = CRUDBooster::getSetting('logo_recibointerno');

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

    <div class="ticket-container">
        <div class="text-center">
            <br>
            <br>
             @if ($imagen_base64)
       <img src="data:image/{{ $tipo_imagen }};base64, {{ $imagen_base64 }}" alt="Logo del Reporte" class="logo" alt="Logo">
    @else
       <div style="width: 100px; height: 100px; line-height: 100px; border: 1px dashed #ccc; margin: 0 auto; text-align: center;">[LOGO NO DISPONIBLE]</div>
    @endif

            <p class="bold" style="font-size: 13px;">{{CRUDBooster::getSetting('nombre_corto_del_establecimiento')}}</p>
            <p>{{CRUDBooster::getSetting('direccion_recibointerno')}}</p>

            <p>{{CRUDBooster::getSetting('telefono_recibointerno')}}</p>
        </div>

        <div class="border-top"></div>

        <div class="text-left">
            <p><strong>Recibo:</strong> {{ $numero_recibo }}</p>
            <p><strong>Fecha:</strong> {{ $fecha_certificacion }}</p>
            <p><strong>NIT:</strong> {{ $nit }}</p>
            <p><strong>Cliente:</strong> {{ $cliente }}</p>
            <p><strong>Dirección:</strong> {{ $direccion }}</p>
        </div>

        <div class="border-top"></div>

        <table>
            <thead>
                <tr>

                    <th>Cant</th>
                    <th>Desc</th>
                    <th>Prec</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                <tr>

                    <td>{{ $item['Cantidad'] }}</td>
                    <td>{{ $item['Descripcion'] }}</td>
                    <td>Q{{ number_format($item['PrecioUnitario'], 2) }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-top"></div>

        @php
            $metodosLimpios = implode(', ', array_unique(array_map('trim', explode(',', $metodo_pago))));
        @endphp



         <div class="text-right">
          <p class="bold">Método de Pago: {{ $metodosLimpios }}</p>
        </div>

        <div class="text-right">
            <p class="bold">Total: Q{{ number_format($total, 2) }}</p>
        </div>

        <div class="text-left">
            <img src="data:image/png;base64,{{ $qr_code }}" class="qr-code" alt="QR Code">
        </div>

        <div class="text-left">

            <p style="font-size: 9px;">Carné: {{ $carnet }}</p>
            <p style="font-size: 9px;">Alumno: {{ $alumno }}</p>
            <p style="font-size: 9px;">Cod. Familia: {{ $familia }}</p>
        </div>

        <div class="text-center">
            <p>Gracias por su pago</p>
        </div>
    </div>
</body>
</html>
