@extends('crudbooster::admin_template')


@section('content')
    <h1>Detalle Grado-Carrera</h1>

    <div>
        <p>ID: {{ $gradoCarrera->id }}</p>
        <p>Grado: {{ $gradoCarrera->grado->nombre }}</p>
        <p>Carrera: {{ $gradoCarrera->carrera->nombre }}</p>
        <p>Estado: {{ $gradoCarrera->estado->estado }}</p>
    </div>

    <a href="{{ route('grado-carreras.index') }}">Volver</a>
@endsection
