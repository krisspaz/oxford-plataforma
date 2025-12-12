@extends('crudbooster::admin_template')
@section('content')

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><strong>📄 Detalles de la Factura SAT con Serie: {{ $facturas->first()->serie ?? 'N/A' }}</strong></h3>
        <div class="box-tools pull-right">
            <a href="{{ route('reportes.facturas.index') }}" class="btn btn-sm btn-default">
                <i class="fa fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>

    <div class="box-body">
        @foreach ($facturas as $factura)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title">🧾 Factura SAT #{{ $factura->numero }}</h4>
                </div>
                <div class="box-body">
                    <div class="row">

                        <div class="col-md-6">
                            <h5 class="text-primary">Información de la Factura</h5>
                            <table class="table table-borderless">
                                <tr><th>Número:</th><td>{{ $factura->numero }}</td></tr>
                                <tr><th>NIT:</th><td>{{ $factura->nit }}</td></tr>
                                <tr><th>GUID:</th><td>{{ $factura->guid }}</td></tr>
                                <tr><th>Fecha de Emisión:</th><td>{{ \Carbon\Carbon::parse($factura->created_at)->format('d/m/Y H:i') }}</td></tr>
                          
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-primary">Información del Pago</h5>
                            <table class="table table-borderless">
                                <tr><th>ID del Pago:</th><td>{{ $factura->pago->id ?? 'N/A' }}</td></tr>
                                <tr>
                                    <tr><th>Producto:</th><td>
                                        @foreach($factura->pago->cuotas as $cuota)
                                        {{ $cuota->productoSeleccionado->detalle->nombre }}
                                        @if(strtolower($cuota->productoSeleccionado->detalle->nombre) === 'mensualidad')
                                            - {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F') }}
                                        @endif
                                      </td></tr>
                                        @endforeach
                                    <th>Métodos y Montos:</th>
                                    <td>
                                        @if($factura->pago && $factura->pago->pagoMetodos)
                                            <ul>
                                                @foreach($factura->pago->pagoMetodos as $metodo)
                                                    <li>{{ $metodo->metodo_pago ?? 'Método desconocido' }}: Q{{ number_format($metodo->monto, 2) }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr><th>Total:</th><td>Q {{ number_format($factura->pago->pagoMetodos->sum('monto') ?? 0, 2) }}</td></tr>
                                <tr><th>Fecha de Pago:</th><td>{{ \Carbon\Carbon::parse($factura->pago->fecha_pago ?? $factura->created_at)->format('d/m/Y H:i:s') }}</td></tr>
                               
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        {{-- Botón único para descargar todos los pagos en un solo PDF --}}
        <div class="text-center" style="margin-top: 20px;">
            <a href="{{ route('reportes.facturas.descargarPDF', ['serie' => $factura->serie]) }}" class="btn btn-success btn-lg" target="_blank">
                <i class="fa fa-file-pdf-o"></i> Descargar PDF
            </a>
        </div>
    </div>
</div>

@endsection
