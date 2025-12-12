@extends('crudbooster::admin_template')

@section('title', 'Inscripcion de ALumnos')


@section('content')
<div class="container">
    <h1>Detalles de la Inscripción</h1>

    <h2>Información del Padre</h2>
    <p><strong>Nombre:</strong> {{ $padre->nombre }}</p>
    <p><strong>Apellido:</strong> {{ $padre->apellido }}</p>
    <p><strong>Tipo de Documento:</strong> {{ $padre->identificacionDocumento->nombre }}</p>
    <p><strong>Número de Documento:</strong> {{ $padre->num_documento }}</p>
    <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($padre->fecha_nacimiento)->format('d M Y') }}</p>
    <p><strong>Profesión:</strong> {{ $padre->profesion }}</p>
    <p><strong>Teléfono:</strong> {{ $padre->telefono }}</p>
    <p><strong>Dirección:</strong> {{ $padre->direccion }}</p>
    <p><strong>Municipio:</strong> {{ $padre->municipio->municipio }}</p>

    <h2>Información de la Madre</h2>
    <p><strong>Nombre:</strong> {{ $madre->nombre }}</p>
    <p><strong>Apellido:</strong> {{ $madre->apellido }}</p>
    <p><strong>Tipo de Documento:</strong> {{ $madre->identificacionDocumento->nombre }}</p>
    <p><strong>Número de Documento:</strong> {{ $madre->num_documento }}</p>
    <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($madre->fecha_nacimiento)->format('d M Y') }}</p>
    <p><strong>Profesión:</strong> {{ $madre->profesion }}</p>
    <p><strong>Teléfono:</strong> {{ $madre->telefono }}</p>
    <p><strong>Dirección:</strong> {{ $madre->direccion }}</p>
    <p><strong>Municipio:</strong> {{ $madre->municipio->municipio }}</p>

    <h2>Información del Encargado</h2>
    <p><strong>Nombre:</strong> {{ $encargado->nombre }}</p>
    <p><strong>Apellido:</strong> {{ $encargado->apellido }}</p>
    <p><strong>Tipo de Documento:</strong> {{ $encargado->identificacionDocumento->nombre }}</p>
    <p><strong>Número de Documento:</strong> {{ $encargado->num_documento }}</p>
    <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($encargado->fecha_nacimiento)->format('d M Y') }}</p>
    <p><strong>Profesión:</strong> {{ $encargado->profesion }}</p>
    <p><strong>Teléfono:</strong> {{ $encargado->telefono }}</p>
    <p><strong>Dirección:</strong> {{ $encargado->direccion }}</p>
    <p><strong>Municipio:</strong> {{ $encargado->municipio->municipio }}</p>

    <h1>Detalle del Alumno</h1>
    <table class="table">
        <tr>
            <th>ID</th>
            <td>{{ $alumno->id }}</td>
        </tr>
        <tr>
            <th>Código</th>
            <td>{{ $alumno->codigo }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $alumno->nombre }}</td>
        </tr>
        <tr>
            <th>Apellidos</th>
            <td>{{ $alumno->apellidos }}</td>
        </tr>
        <tr>
            <th>Género</th>
            <td>{{ $alumno->genero }}</td>
        </tr>
        <tr>
            <th>CUI</th>
            <td>{{ $alumno->cui }}</td>
        </tr>
        <tr>
            <th>Fecha de Nacimiento</th>
            <td>{{ $alumno->fecha_nacimiento }}</td>
        </tr>
        <tr>
            <th>Municipio</th>
            <td>{{ $alumno->municipio->municipio }}</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td>{{ $alumno->direccion }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $alumno->telefono }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $alumno->estado->estado }}</td>
        </tr>
    </table>

    <a href="{{ route('inscripcion.edit', $inscripcion->id) }}" class="btn btn-primary">Editar</a>
    <form action="{{ route('inscripcion.destroy', $inscripcion->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    <a href="{{ route('inscripcion.index') }}" class="btn btn-success">Volver</a>
</div>
@endsection
