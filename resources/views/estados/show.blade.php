@extends('crudbooster::admin_template')

@section('content')
    <h1>Detalle del Estado</h1>
    <div>
        <strong>ID:</strong> {{ $estado->id }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $estado->estado }}
    </div>
    <a href="{{ route('estados.index') }}">Volver</a>
@endsection
