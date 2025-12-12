<!-- resources/views/detalle_productos/show.blade.php -->
@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <h1>Detalle del Producto</h1>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $detalleProducto->id }}</td>
        </tr>
        <tr>
            <th>Paquete Detalle</th>
            <td>{{ $detalleProducto->paqueteDetalle->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Producto</th>
            <td>{{ $detalleProducto->producto->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Cantidad</th>
            <td>{{ $detalleProducto->cantidad }}</td>
        </tr>
    </table>

    <a href="{{ route('detalle_productos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
