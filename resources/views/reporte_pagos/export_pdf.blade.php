<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
    <title>Reporte de Pagos</title>
</head>
<body>
    <h3>Reporte de Pagos</h3>
    <p><strong>Total:</strong> Q. {{ number_format($totalFiltrado, 2) }}</p>
    <table>
        <thead>
            <tr>
                <th>Fecha de Pago</th>
                <th>Tipo de Pago</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pagos as $pago)
            <tr>
                <td>{{ $pago->fecha_pago }}</td>
                <td>{{ $pago->tipo_pago }}</td>
                <td>Q. {{ number_format($pago->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
