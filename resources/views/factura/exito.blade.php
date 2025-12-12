@extends('crudbooster::admin_template')



@section('content')
<div class="container">
    <h2>Factura Certificada Exitosamente</h2>
    <p>La factura ha sido certificada con éxito.</p>
    <p><strong>Document GUID:</strong> {{ session('guid') }}</p>
    <p><strong>Serie:</strong> {{ session('serie') }}</p>
    <p><strong>Número:</strong> {{ session('numero') }}</p>
    <p>
        <strong>Link de la factura:</strong>
        <a href="{{ session('link') }}" target="_blank" download class="btn btn-primary">
            Descargar Factura
        </a>
    </p>
</div>
@endsection
