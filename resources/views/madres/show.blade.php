@extends('crudbooster::admin_template')



@section('content')
    <h1>Detalles de Madre</h1>
    <p><strong>Nombre:</strong> {{ $madre->nombre }}</p>
    <p><strong>Apellido:</strong> {{ $madre->apellido }}</p>
    <p><strong>Documento de Identificación:</strong> {{ $madre->identificacionDocumento->nombre }}</p>
    <p><strong>Número de Documento:</strong> {{ $madre->num_documento }}</p>
    <p><strong>Fecha de Nacimiento:</strong> {{ $madre->fecha_nacimiento }}</p>
    <p><strong>Profesión:</strong> {{ $madre->profesion }}</p>
    <p><strong>Teléfono:</strong> {{ $madre->telefono }}</p>
    <p><strong>Municipio:</strong> {{ $madre->municipio->municipio }}</p>
    <p><strong>Dirección:</strong> {{ $madre->direccion }}</p>
    <a href="{{ route('madres.index') }}">Volver a la lista</a>
    <a href="{{ route('madres.edit', $madre->id) }}">Editar</a>
    <form action="{{ route('madres.destroy', $madre->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit">Eliminar</button>
    </form>
@endsection
