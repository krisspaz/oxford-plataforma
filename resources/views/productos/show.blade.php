@extends('crudbooster::admin_template')

@section('content')
<div class="box">
 
    <div class="box-header">
        <h1 class="box-title">Detalle del Producto</h1>
   
    <table class="table">
        <tr>
            <th>Nombre</th>
            <td>{{ $producto->nombre }}</td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td>{{ $producto->descripcion }}</td>
        </tr>
        <tr>
            <th>Precio</th>
            <td>{{ $producto->precio }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $producto->estado->estado }}</td>
        </tr>
    </table>
</div>
</div>

    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
    
    
@endsection