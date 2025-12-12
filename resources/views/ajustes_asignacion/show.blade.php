<!-- resources/views/ajustes_asignacion/show.blade.php -->
@extends('crudbooster::admin_template')

@section('content')
<div class="container">
    <h2>Detalles del Estudiante</h2>

    <div class="card">
        <div class="card-header">
            {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
        </div>
        <div class="card-body">
            <p><strong>Nivel:</strong> {{ $estudiante->nivel->nombre }}</p>
            <p><strong>CGSHGE:</strong> {{ $estudiante->cgshges->nombre }}</p>
            <p><strong>Estado:</strong> {{ $estudiante->estado->nombre }}</p>
            <p><strong>Foto:</strong></p>
            <img src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}" alt="Foto Estudiante" width="150">

            <a href="{{ route('ajustes_asignacion.index') }}" class="btn btn-secondary mt-3">Volver</a>
        </div>
    </div>
</div>
@endsection
