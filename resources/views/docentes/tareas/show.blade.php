@extends('crudbooster::admin_template')

@section('content')
<div class="container">
    <h1>Detalle de la Tarea</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $tarea->titulo }}</h5>
            <p class="card-text"><strong>Descripción:</strong> {{ $tarea->descripcion }}</p>
            <p class="card-text"><strong>Materia:</strong> {{ $tarea->materia->gestionMateria->nombre }}</p>
            <p class="card-text"><strong>Fecha de Expiración:</strong> {{ $tarea->fexpiracion }}</p>
            <p class="card-text"><strong>Estado:</strong> {{ $tarea->estado->estado }}</p>
            <a href="{{ route('docentes.tareas.edit', $tarea->id) }}" class="btn btn-warning">Editar</a>
            <a href="{{ route('docentes.tareas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection
