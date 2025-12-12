@extends('crudbooster::admin_template')



@section('content')
    <h1>Detalle del Día</h1>
    <div>
        <strong>ID:</strong> {{ $dia->id }}
    </div>
    <div>
        <strong>Nombre:</strong> {{ $dia->nombre }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $dia->estado->estado }}
    </div>
    <a href="{{ route('dias.index') }}">Volver</a>
@endsection
