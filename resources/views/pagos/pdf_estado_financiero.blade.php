<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado Financiero</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
        }

        .container {
            page-break-inside: avoid;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 70px;
            height: auto;
        }

        .header .titulo {
            font-size: 20px;
            font-weight: bold;
            margin: 2px 0;
        }

        .header p {
            margin: 1px 0;
            font-size: 12px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0 4px 0;
        }

        .info-box {
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 5px;
            background-color: #f5f5f5;
            margin-bottom: 10px;
        }

        .info-box h4 {
            margin: 3px 0;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead {
            background-color: #eee;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        tfoot td {
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }

        /* DOMPDF-specific fixes */
        table, tr, td, th, thead, tbody, tfoot {
            page-break-inside: auto !important;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
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

<div class="container">
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

    <div class="section-title">Estado Financiero del Estudiante</div>

    <div class="info-box">
        <h4><strong>Carné:</strong> {{ $convenio->inscripcion->estudiante->carnet }}</h4>
        <h4><strong>Estudiante:</strong> {{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</h4>
        <h4><strong>Grado:</strong> {{ $convenio->inscripcion->estudiante->cgshges->grados->nombre }}</h4>
        <h4><strong>Curso:</strong> {{ $convenio->inscripcion->estudiante->cgshges->cursos->curso }}</h4>
        <h4><strong>Jornada:</strong> {{ $convenio->inscripcion->estudiante->cgshges->jornadas->jornada->nombre }}</h4>
        <h4><strong>Paquete Escolar:</strong> {{ $convenio->inscripcion->paquete->nombre }}</h4>
        <h4><strong>No. Referencia / Convenio:</strong> {{ $convenio->id }}</h4>
    </div>

    <div class="section-title">Detalle de Cuotas</div>

    @php $total = 0; @endphp
    <table>
        <thead>
            <tr>
                <th>Producto o Servicio</th>
                <th>Monto Total (Q)</th>
                <th>Fecha de Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($convenio->cuotas as $cuota)
                @php
                    $nombre = strtolower($cuota->productoSeleccionado->detalle->nombre ?? '') == 'mensualidad'
                        ? 'Mensualidad ' . ucfirst(\Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F'))
                        : ($cuota->productoSeleccionado->detalle->nombre ?? 'N/A');
                    $total += $cuota->monto_cuota;
                @endphp
                <tr>
                    <td>{{ $nombre }}</td>
                    <td>{{ number_format($cuota->monto_cuota, 2) }}</td>
                    <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td>{{ $cuota->estado }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="1" style="text-align: right;">Total</td>
                <td>{{ number_format($total, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
</div>

</body>
</html>
