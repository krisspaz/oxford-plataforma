@extends('crudbooster::admin_template')

@section('title', 'Encargados')

@section('content')
<div class="container">
    <h1>Detalles del Encargado</h1>
    <div class="card">
        <div class="card-header">
            <h2>{{ $encargado->nombre }} {{ $encargado->apellido }}</h2>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $encargado->id }}</p>
            <p><strong>Parentesco:</strong> {{ $encargado->parentesco->parentesco }}</p>
            <p><strong>Documento de Identificación:</strong> {{ $encargado->identificacionDocumento->nombre }}</p>
            <p><strong>Número de Documento:</strong> {{ $encargado->num_documento }}</p>
            <p><strong>Fecha de Nacimiento:</strong> {{ $encargado->fecha_nacimiento }}</p>
            <p><strong>Profesión:</strong> {{ $encargado->profesion }}</p>
            <p><strong>Teléfono:</strong> {{ $encargado->telefono }}</p>
            <p><strong>Municipio:</strong> {{ $encargado->municipio->municipio }}</p>
            <p><strong>Departamento:</strong> {{ $encargado->municipio->departamento->departamento }}</p>
            <p><strong>Dirección:</strong> {{ $encargado->direccion }}</p>
        </div>
    </div>
    <a href="{{ route('encargados.index') }}" class="btn btn-primary mt-3">Volver a la lista</a>
</div>
@endsection
