@extends('crudbooster::admin_template')

@section('content')
    <h1>Detalle del Nivel</h1>
    <div>
        <strong>ID:</strong> {{ $nivele->id }}
    </div>
    <div>
        <strong>Nombre:</strong> {{ $nivele->nombre }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $nivele->estado->estado }}
    </div>
    <a href="{{ route('niveles.index') }}">Volver</a>
@endsection
