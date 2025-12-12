@extends('crudbooster::admin_template')


@section('content')
    <h1>Detalle del Departamento</h1>

    <div>
        <p>ID: {{ $departamento->id }}</p>
        <p>Departamento: {{ $departamento->departamento }}</p>
        <p>Estado: {{ $departamento->estado->estado }}</p>
    </div>

    <a href="{{ route('departamentos.index') }}">Volver</a>
@endsection
