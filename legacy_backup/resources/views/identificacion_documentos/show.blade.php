@extends('crudbooster::admin_template')



@section('content')
    <div class="container">
        <h1>Detalles del Documento de Identificación</h1>
        
        <div class="form-group">
            <label>Nombre:</label>
            <p>{{ $identificacionDocumento->nombre }}</p>
        </div>
        
        <div class="form-group">
            <label>Descripción:</label>
            <p>{{ $identificacionDocumento->descripcion }}</p>
        </div>
        
        <a href="{{ route('identificacion_documentos.edit', $identificacionDocumento->id) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('identificacion_documentos.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
