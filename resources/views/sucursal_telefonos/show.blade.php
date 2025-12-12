<!-- resources/views/sucursal_telefonos/show.blade.php -->

@extends('crudbooster::admin_template')

@section('content')
    <div class="container">
        <h1>Detalle de la Relación Sucursal-Telefono</h1>
        <div class="form-group">
            <label for="sucursal_id">Sucursal:</label>
            <p>{{ $sucursalTelefono->sucursal->nombre_sucursal }}</p>
        </div>
        <div class="form-group">
            <label for="telefono_id">Telefono:</label>
            <p>{{ $sucursalTelefono->telefono->telefono }}</p>
        </div>
        <a href="{{ route('sucursal_telefonos.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
