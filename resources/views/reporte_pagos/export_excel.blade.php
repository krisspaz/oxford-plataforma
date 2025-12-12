<table>
    <thead>
        <tr>
            <th>Fecha de Pago</th>
            <th>Tipo de Pago</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->fecha_pago }}</td>
                <td>{{ $pago->tipo_pago }}</td>
                <td>{{ $pago->monto }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
