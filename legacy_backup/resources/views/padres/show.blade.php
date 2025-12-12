@extends('crudbooster::admin_template')



@section('content')
    <div class="container">
        <h1>Detalles del Padre</h1>
        
        <div class="form-group">
            <label>Nombre:</label>
            <p>{{ $padre->nombre }}</p>
        </div>
        
        <div class="form-group">
            <label>Apellido:</label>
            <p>{{ $padre->apellido }}</p>
        </div>
        
        <div class="form-group">
            <label>Documento:</label>
            <p>{{ $padre->identificacionDocumento->nombre }}: {{ $padre->num_documento }}</p>
        </div>
        
        <div class="form-group">
            <label>Fecha de Nacimiento:</label>
            <p>{{ $padre->fecha_nacimiento }}</p>
        </div>
        
        <div class="form-group">
            <label>Profesión:</label>
            <p>{{ $padre->profesion }}</p>
        </div>
        
        <div class="form-group">
            <label>Teléfono:</label>
            <p>{{ $padre->telefono }}</p>
        </div>
        
        <div class="form-group">
            <label>Municipio:</label>
            <p>{{ $padre->municipio->municipio }}</p>
        </div>
        
        <div class="form-group">
            <label>Dirección:</label>
            <p>{{ $padre->direccion }}</p>
        </div>
        
        <a href="{{ route('padres.edit', $padre->id) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('padres.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
