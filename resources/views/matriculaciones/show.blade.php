@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles de Matriculación</h1>
    <table class="table">
        <tr>
            <th>ID</th>
            <td>{{ $matriculacion->id }}</td>
        </tr>
        <tr>
            <th>Estudiante</th>
            <td>{{ $matriculacion->estudiante->nombre }}</td>
        </tr>
        <tr>
            <th>Paquete</th>
            <td>{{ $matriculacion->paquete->nombre }}</td>
        </tr>
        <tr>
            <th>Fecha de Inscripción</th>
            <td>{{ $matriculacion->fecha_inscripcion }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $matriculacion->estado->nombre }}</td>
        </tr>
        <tr>
            <th>Registrado por</th>
            <td>{{ $matriculacion->cmsUser->name ?? 'N/A' }}</td>
        </tr>
    </table>
    <a href="{{ route('matriculaciones.index') }}" class="btn btn-secondary">Regresar</a>
</div>
@endsection
