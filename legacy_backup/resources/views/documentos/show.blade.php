@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Detalle del Documento</h3>
    </div>
    <div class="box-body">
        <p><strong>Estudiante:</strong> {{ $documento->estudiante->nombre ?? 'N/A' }}</p>
        <p><strong>Tipo:</strong> {{ $documento->tipo_documento }}</p>
        <p><strong>Nombre:</strong> {{ $documento->nombre_documento }}</p>
        <p><strong>Fecha de Expiración:</strong> {{ $documento->fexpiracion }}</p>
        <p><strong>Estado:</strong> {{ $documento->estado->estado ?? 'N/A' }}</p>
        <p><strong>Archivo:</strong>
            <a href="{{ asset('storage/' . $documento->documento) }}" target="_blank" class="btn btn-primary btn-xs">Ver Documento</a>
        </p>
        <a href="{{ route('documentos.index') }}" class="btn btn-default">Volver</a>
    </div>
</div>
@endsection
