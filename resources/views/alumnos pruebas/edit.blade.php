
@extends('crudbooster::admin_template')



@section('title', 'Municipio List')

@section('content')
    <h1>Editar Alumno</h1>

    <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h2>Información del Padre</h2>
        <label for="padre_nombre">Nombre:</label>
        <input type="text" id="padre_nombre" name="padre_nombre" value="{{ $alumno->padre->nombre ?? '' }}">
        <label for="padre_apellido">Apellido:</label>
        <input type="text" id="padre_apellido" name="padre_apellido" value="{{ $alumno->padre->apellido ?? '' }}">
        <label for="padre_email">Email:</label>
        <input type="email" id="padre_email" name="padre_email" value="{{ $alumno->padre->email ?? '' }}">
        <label for="padre_telefono">Teléfono:</label>
        <input type="text" id="padre_telefono" name="padre_telefono" value="{{ $alumno->padre->telefono ?? '' }}">
        <label for="padre_direccion">Dirección:</label>
        <input type="text" id="padre_direccion" name="padre_direccion" value="{{ $alumno->padre->direccion ?? '' }}">

        <h2>Información de la Madre</h2>
        <label for="madre_nombre">Nombre:</label>
        <input type="text" id="madre_nombre" name="madre_nombre" value="{{ $alumno->madre->nombre ?? '' }}">
        <label for="madre_apellido">Apellido:</label>
        <input type="text" id="madre_apellido" name="madre_apellido" value="{{ $alumno->madre->apellido ?? '' }}">
        <label for="madre_email">Email:</label>
        <input type="email" id="madre_email" name="madre_email" value="{{ $alumno->madre->email ?? '' }}">
        <label for="madre_telefono">Teléfono:</label>
        <input type="text" id="madre_telefono" name="madre_telefono" value="{{ $alumno->madre->telefono ?? '' }}">
        <label for="madre_direccion">Dirección:</label>
        <input type="text" id="madre_direccion" name="madre_direccion" value="{{ $alumno->madre->direccion ?? '' }}">

        <h2>Información del Encargado</h2>
        <label for="encargado_nombre">Nombre:</label>
        <input type="text" id="encargado_nombre" name="encargado_nombre" value="{{ $alumno->encargado->nombre ?? '' }}">
        <label for="encargado_apellido">Apellido:</label>
        <input type="text" id="encargado_apellido" name="encargado_apellido" value="{{ $alumno->encargado->apellido ?? '' }}">
        <label for="encargado_email">Email:</label>
        <input type="email" id="encargado_email" name="encargado_email" value="{{ $alumno->encargado->email ?? '' }}">
        <label for="encargado_telefono">Teléfono:</label>
        <input type="text" id="encargado_telefono" name="encargado_telefono" value="{{ $alumno->encargado->telefono ?? '' }}">
        <label for="encargado_direccion">Dirección:</label>
        <input type="text" id="encargado_direccion" name="encargado_direccion" value="{{ $alumno->encargado->direccion ?? '' }}">

        <h2>Información del Alumno</h2>
        <label for="alumno_codigo">Código:</label>
        <input type="text" id="alumno_codigo" name="alumno_codigo" value="{{ $alumno->codigo }}" required>
        <label for="alumno_nombre">Nombre:</label>
        <input type="text" id="alumno_nombre" name="alumno_nombre" value="{{ $alumno->nombre }}" required>
        <label for="alumno_apellido">Apellido:</label>
        <input type="text" id="alumno_apellido" name="alumno_apellido" value="{{ $alumno->apellido }}" required>
        <label for="alumno_fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="alumno_fecha_nacimiento" name="alumno_fecha_nacimiento" value="{{ $alumno->fecha_nacimiento->format('Y-m-d') }}" required>
        <label for="alumno_grado">Grado:</label>
        <input type="text" id="alumno_grado" name="alumno_grado" value="{{ $alumno->grado }}" required>

        <button type="submit">Guardar Cambios</button>
    </form>
    @endsection
