<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
        }
        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #dddddd;
            padding: 3px;
            text-align: left;
        }
        table th {
            background-color: #4a79f8;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid #ddd;
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
    <div class="titulo">Reporte de Pagos</div>



    <!-- Tabla de Pagos -->
    <div class="table-section">
       <!-- Pagos Desglosados con Detalles -->
<div class="table-section">
    <h3>Pagos Desglosados</h3>
    <table>
        <thead>
            <tr>
                <th>ID Pago</th>


                <th>Carné</th>
                <th>Estudiante</th>
                <th>Producto</th>
                <th>Monto</th>
                <th>Fecha de Pago</th>

                <th>Tipo de Pago</th>

                <th>Metodo de Pago</th>
                <th>Detalles</th>
                <th>Comprobante</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($datosPagos as $pago)
                @foreach ($pago['metodos_de_pago'] as $metodo)
                    <tr>
                        <td>{{ $pago['id'] }}</td>
                        <td>{{ $pago['inscripcion'] ['estudiante']->carnet}}</td>
                        <td>{{ $pago['inscripcion'] ['estudiante'] ['persona']->nombres}} {{ $pago['inscripcion'] ['estudiante'] ['persona']->apellidos}}<</td>

                        @foreach ($pago['cuotas'] as $cuota)
                            <td>{{ $cuota['producto_seleccionado'] ? $cuota['producto_seleccionado']['nombre'] : 'N/A' }}</td>
                        @endforeach



                        <td>{{ number_format($metodo['monto'], 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($pago['fecha_pago'])->format('d/m/Y') }}</td>


                        <td>{{ $pago['tipo_pago'] }}</td>



                        <!-- Mostrar método de pago y detalles -->
                        <td>{{ $metodo['metodo'] }}</td>

                        <td>
                            @if (is_array($metodo['detalles']) && !empty($metodo['detalles']))
                                @foreach ($metodo['detalles'] as $key => $value)
                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                                @endforeach
                            @else

                            @endif
                        </td>

                        <td>
                            {{ $pago['tipo_comprobante'] }}
                        </td>

                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

    </div>






  <!-- Métodos de Pago -->
<div class="table-section">
    <h3>Métodos de Pago</h3>
    @php
        $metodosPagoAgrupados = []; // Array para almacenar los métodos de pago agrupados
    @endphp
    @foreach ($datosPagos as $pago)
        @foreach ($pago['metodos_de_pago'] as $metodo)
            @php
                // Verifica si el método ya existe en el array
                if (!isset($metodosPagoAgrupados[$metodo['metodo']])) {
                    $metodosPagoAgrupados[$metodo['metodo']] = [
                        'monto' => 0,
                       'detalles' => is_string($metodo['detalles']) ? json_decode($metodo['detalles'], true) : $metodo['detalles']

                    ];
                }
                // Acumula el monto de ese método de pago
                $metodosPagoAgrupados[$metodo['metodo']]['monto'] += $metodo['monto'];
                $totalMetodosPago += $metodo['monto']; // Acumula el total general
            @endphp
        @endforeach
    @endforeach

    <!-- Mostrar los métodos de pago agrupados -->
    <table>
        <thead>
            <tr>
                <th>Metodo de Pago</th>
                <th>Monto Total</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($metodosPagoAgrupados as $metodo => $datos)
                <tr>
                    <td>{{ $metodo }}</td>
                    <td>{{ number_format($datos['monto'], 2) }}</td>
                    <!-- Mostrar detalles del JSON si es un array válido -->

                </tr>
            @endforeach

             <!-- Fila con el total de todos los métodos de pago -->
             <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ number_format($totalMetodosPago, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>


 <!-- Pie de Página con Fecha y Hora -->
 <div class="footer">
    Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
</div>


</body>

</html>
