@extends('crudbooster::admin_template')

@section('content')
    <h1>Respuesta del Servicio Web</h1>

    <pre>
        {{ $response }}
    </pre>
@endsection