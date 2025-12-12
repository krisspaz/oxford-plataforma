<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos Pendientes - {{ $estudiante->persona->nombres }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { max-width: 100px; margin-bottom: 5px; }
        .header .titulo { font-size: 18px; font-weight: bold; margin: 5px 0; }
        .section-title { background-color: #f2f2f2; font-weight: bold; padding: 5px; margin-top: 20px; }
        table { width: 100%; margin-top: 5px; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #ddd; text-align: left; }
        td.text-right { text-align: right; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
        .total-row { font-weight: bold; background-color: #f7f7f7; }
        .gran-total { font-weight: bold; font-size: 14px; margin-top: 10px; text-align: right; }
    </style>
</head>
<body>

    @php
        $ruta_relativa_bd = CRUDBooster::getSetting('logo_reporte');
        $ruta_logo_completa = storage_path('app/' . $ruta_relativa_bd);
        $imagen_base64 = null;
        $tipo_imagen = 'png';
        if (!empty($ruta_relativa_bd) && file_exists($ruta_logo_completa)) {
            $tipo_imagen = pathinfo($ruta_logo_completa, PATHINFO_EXTENSION);
            $imagen_base64 = base64_encode(file_get_contents($ruta_logo_completa));
        }
        $granTotal = 0; // Total global de todos los ciclos
    @endphp

    <div class="header">
        @if($imagen_base64)
            <img src="data:image/{{ $tipo_imagen }};base64, {{ $imagen_base64 }}" alt="Logo">
        @else
            <div style="width:100px;height:100px;border:1px dashed #ccc;line-height:100px;margin:0 auto;text-align:center;">[LOGO]</div>
        @endif
        <div class="titulo">{{ CRUDBooster::getSetting('nombre_del_establecimiento') }}</div>
        <div>{{ CRUDBooster::getSetting('direccion_del_establecimiento') }}</div>
        <div>Teléfono: {{ CRUDBooster::getSetting('numero_de_telefono') }}</div>
    </div>

    <div class="section-title">Estudiante</div>
    <table>
        <tr><th>Nombre</th><td>{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</td></tr>
        <tr><th>Carné</th><td>{{ $estudiante->carnet ?? $estudiante->carne ?? 'N/A' }}</td></tr>
    </table>

    @foreach($detalles as $detalle)
        <div class="section-title">Ciclo Escolar: {{ $detalle['ciclo'] }}</div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Monto (Q)</th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                @php $totalCiclo = 0; @endphp
                @foreach($detalle['cuotas'] as $cuota)
                    @php
                        $nombreProducto = ucfirst(optional(optional($cuota->productoSeleccionado)->detalle)->nombre ?? 'N/A');
                        try {
                            $fecha = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y');
                        } catch (\Exception $e) { $fecha = 'N/A'; }
                        $monto = floatval($cuota->monto_cuota ?? ($cuota->monto ?? 0));
                        $totalCiclo += $monto;
                    @endphp
                    <tr>
                        <td>{{ $nombreProducto }}</td>
                        <td class="text-right">Q {{ number_format($monto, 2) }}</td>
                        <td>{{ $fecha }}</td>
                    </tr>
                @endforeach

                {{-- Fila de total por ciclo --}}
                <tr class="total-row">
                    <td>Monto Total</td>
                    <td class="text-right">Q {{ number_format($totalCiclo, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        @php $granTotal += $totalCiclo; @endphp
    @endforeach

    {{-- Gran total de todos los ciclos --}}
    <div class="gran-total">
        Gran Total: Q {{ number_format($granTotal, 2) }}
    </div>

</body>
</html>
