@extends('crudbooster::admin_template')



@section('title', 'Municipio List')

@section('content')

    <title>Detalles del Alumno</title>


    <h1>Detalles del Alumno</h1>

    <p>Codigo: {{ $alumno->codigo }} </p>
    <p>Nombre: {{ $alumno->nombre }} {{ $alumno->apellido }}</p>
    <p>Fecha de Nacimiento: {{ $alumno->fecha_nacimiento }}</p>
    <p>Grado: {{ $alumno->grado }}</p>
    <p>Padre: {{ $alumno->padre ? $alumno->padre->nombre . ' ' . $alumno->padre->apellido : 'Ninguno' }}</p>
    <p>Madre: {{ $alumno->madre ? $alumno->madre->nombre . ' ' . $alumno->madre->apellido : 'Ninguno' }}</p>
    <p>Encargado: {{ $alumno->encargado ? $alumno->encargado->nombre . ' ' . $alumno->encargado->apellido : 'Ninguno' }}</p>

    <a href="{{ route('alumnos.index') }}">Volver a la lista</a>

@endsection
