@extends('crudbooster::admin_template')

@section('content')
    <div class="container">
        <h1>Detalles del Nivel Sucursal</h1>
        
        <div class="form-group">
            <label>Sucursal:</label>
            <p>{{ $niveles_sucursal->sucursal->nombre_sucursal }}</p>
        </div>
        
        <div class="form-group">
            <label>Nivel:</label>
            <p>{{ $niveles_sucursal->nivel->nombre }}</p>
        </div>
        
        <div class="form-group">
            <label>Estado:</label>
            <p>{{ $niveles_sucursal->estado->estado }}</p>
        </div>
        
        <a href="{{ route('niveles_sucursals.edit', $niveles_sucursal->id) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('niveles_sucursals.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
