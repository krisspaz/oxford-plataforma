@extends('crudbooster::admin_template')

@section('content')
<div class="container">
    <h1>Detalles del Docente</h1>
    <a href="{{ route('docentes.index') }}" class="btn btn-secondary mb-3">Volver</a>

    <div class="card">
        <div class="card-header">
            <h3>{{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Cedula Profesional:</strong> {{ $docente->cedula }}</p>
            <p><strong>Especialidad:</strong> {{ $docente->especialidad }}</p>
            <p><strong>Telefono</strong> {{ $docente->persona->telefono }}</p>
            <p><strong>Dirección</strong> {{ $docente->persona->direccion }}</p>
           
        </div>
    </div>
</div>
@endsection
