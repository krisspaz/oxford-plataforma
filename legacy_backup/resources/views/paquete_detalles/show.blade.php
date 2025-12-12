@extends('crudbooster::admin_template')
@section('content')
<div class="cbox">
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('error') }}
    </div>
@endif
    <h1>Detalle de Paquete</h1>
    <div class="mb-3">
        <strong>Paquete:</strong> {{ $paqueteDetalle->paquete->nombre ?? 'No asignado' }}
    </div>
    <div class="mb-3">
        <strong>Nombre:</strong> {{ $paqueteDetalle->nombre }}
    </div>
    <div class="mb-3">
        <strong>Tipo Comprobante a emitir:</strong>
        @if ($paqueteDetalle->tipo_comprobante === 'Recibo')
            Recibo Electrónico SAT
        @elseif ($paqueteDetalle->tipo_comprobante === 'Factura')
            Factura Electrónica SAT
        @elseif ($paqueteDetalle->tipo_comprobante === 'Comprobante')
            Recibo Interno
        @else
            {{ $paqueteDetalle->tipo_comprobante }}
        @endif
    </div>
    <div class="mb-3">
        <strong>Precio:</strong> Q.{{ number_format($paqueteDetalle->precio, 2) }}
    </div>
    <a href="{{ route('paquete_detalles.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
