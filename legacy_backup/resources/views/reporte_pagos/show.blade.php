@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle del Pago</h3>
        <div class="box-tools">
            <a href="{{ route('reporte_pagos.index') }}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Volver
            </a>

            <a href="{{ route('reporte_pagos.edit', $reporte_pago->id) }}" class="btn btn-sm btn-warning">
                <i class="fa fa-pencil"></i> Editar
            </a>
        </div>
    </div>

    <div class="box-body">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $reporte_pago->id }}</td>
            </tr>
            <tr>
                <th>Convenio</th>
                <td>{{ $reporte_pago->convenio->id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tipo de Pago</th>
                <td>{{ $reporte_pago->tipo_pago }}</td>
            </tr>
            <tr>
                <th>Monto</th>
                <td>Q{{ number_format($reporte_pago->monto, 2) }}</td>
            </tr>
            <tr>
                <th>Exonerado</th>
                <td>{{ $reporte_pago->exonerar }}</td>
            </tr>
            <tr>
                <th>Fecha de Pago</th>
                <td>{{ $reporte_pago->fecha_pago }}</td>
            </tr>

            <tr>
                <th>Comprobante Emitido</th>
                <td>
                    @if ($reporte_pago->facturaEmitida)
                   
                    <a href="{{ route('reportes.facturas.descargarPDF', $reporte_pago->facturaEmitida->serie) }}" class="btn btn-xs btn-success">
                        <i class="fa fa-file-pdf-o"></i> Factura SAT
                    </a>
                @elseif ($reporte_pago->reciboEmitido)
                

                    <a href="{{ route('reportes.recibos.descargarPDF', $reporte_pago->reciboEmitido->serie) }}" class="btn btn-xs btn-success">
                        <i class="fa fa-file-pdf-o"></i> Recibo SAT
                    </a>
                @elseif ($reporte_pago->recibointernoEmitido)
                  
                    <a href="{{ route('reportes.recibosinternos.descargarPDF', $reporte_pago->recibointernoEmitido->serie) }}" class="btn btn-xs btn-success">
                        <i class="fa fa-file-pdf-o"></i> Recibo Interno
                    </a>
                    
                @else
                    <span class="label label-default">Sin comprobante</span>
                @endif
                
                </td>
            </tr>
            {{-- Agrega más campos si tu modelo los tiene --}}
        </table>
    </div>
</div>
@endsection
