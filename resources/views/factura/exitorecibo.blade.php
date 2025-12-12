@extends('crudbooster::admin_template')



@section('content')
<div class="container">
    <h2>Recibo Emitido Exitosamente</h2>
    <p>El Recibo ha sido certificada con éxito.</p>
    <p><strong>Document GUID:</strong> {{ session('guid') }}</p>
    <p><strong>Serie:</strong> {{ session('serie') }}</p>
    <p><strong>Número:</strong> {{ session('numero') }}</p>
    <p>
        <strong>Link del Recibo:</strong>
        <a href="{{ session('link') }}" target="_blank" download class="btn btn-primary">
            Descargar Recibo
        </a>
    </p>
</div>
@endsection
