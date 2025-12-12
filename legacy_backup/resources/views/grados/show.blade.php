@extends('crudbooster::admin_template')

@section('content')
    <h1>Detalle del Grado</h1>
    <div>
        <strong>ID:</strong> {{ $grado->id }}
    </div>
    <div>
        <strong>Nombre:</strong> {{ $grado->nombre }}
    </div>
   
    <div>
        <strong>Estado:</strong> {{ $grado->estado->estado }}
    </div>
    <a href="{{ route('grados.index') }}">Volver</a>
@endsection
