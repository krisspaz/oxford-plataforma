@extends('crudbooster::admin_template')

@section('content')

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Estado Financiero</h3>
    </div>

    <div class="panel-body">
        <h4><strong>Carné:</strong> {{ $convenio->inscripcion->estudiante->carnet }} </h4>
        <h4><strong>Estudiante:</strong> {{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</h4>
        <h4><strong>Grado:</strong> {{ $convenio->inscripcion->estudiante->cgshges->grados->nombre }} </h4>
        <h4><strong>Curso:</strong> {{ $convenio->inscripcion->estudiante->cgshges->cursos->curso }} </h4>
        <h4><strong>Jornada:</strong> {{ $convenio->inscripcion->estudiante->cgshges->jornadas->jornada->nombre }} </h4>
        <h4><strong>Paquete Escolar:</strong> {{ $convenio->inscripcion->paquete->nombre }}</h4>
        <h4><strong>No. Referencia o Convenio:</strong> {{ $convenio->id }}</h4>

        <hr>

        <h4>Detalle de las Cuotas</h4>
        @csrf
        <input type="hidden" name="convenio_id" value="{{$convenio->id}}">
        @php $total = 0; @endphp

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Producto o Servicio</th>
                    <th>Monto Total</th>
                    <th>Fecha de Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($convenio->cuotas as $cuota)
                    @php $total += $cuota->monto_cuota; @endphp
                    <tr>
                        <td>
                            @if (strtolower($cuota->productoSeleccionado->detalle->nombre ?? '') == 'mensualidad')
                                @php $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F'); @endphp
                                Mensualidad {{ ucfirst($mes) }}
                            @else
                                {{ $cuota->productoSeleccionado->detalle->nombre ?? 'N/A' }}
                            @endif
                        </td>
                        <td>{{ number_format($cuota->monto_cuota, 2) }}</td>
                        <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>{{ $cuota->estado }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="1" class="text-right"><strong>Total</strong></td>
                    <td><strong>{{ number_format($total, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <hr>

        <h4>Pagos Realizados</h4>
        @if($convenio->pagos->isEmpty())
            <p>No se han registrado pagos para este convenio.</p>
        @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Fecha de Pago</th>
                    <th>Monto</th>
                    <th>Tipo de Pago</th>
                    <th>Comprobante</th>
                    <th>Métodos de Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($convenio->pagos as $pago)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td>{{ number_format($pago->monto, 2) }}</td>
                        <td>{{ $pago->tipo_pago }}</td>
                        <td>
                            @if($pago->facturaEmitida)
                                Factura No. {{ $pago->facturaEmitida->serie }}
                                @if($pago->facturaEmitida->link)
                                    <a href="{{ $pago->facturaEmitida->link }}" class="btn btn-xs btn-success" target="_blank"> Descargar</a>
                                @endif
                            @elseif($pago->reciboEmitido)
                                Recibo No. {{ $pago->reciboEmitido->serie }}
                                @if($pago->reciboEmitido->link)
                                    <a href="{{ $pago->reciboEmitido->link }}" class="btn btn-xs btn-success" target="_blank"> Descargar</a>

                                </a>

                                    @endif
                            @elseif($pago->recibointernoEmitido)
                                Recibo Interno No. {{ $pago->recibointernoEmitido->serie }}
                                @if($pago->recibointernoEmitido->link)
                                <a href="{{ asset('storage/' . $pago->recibointernoEmitido->link) }}" target="_blank" class="btn btn-xs btn-success">
                                    Descargar
                                </a>
                                    @endif




                            @else
                                Sin comprobante
                            @endif
                        </td>
                        <td>
                            @foreach($pago->pagoMetodos as $metodo)
                                {{ $metodo->metodo_pago }}: {{ number_format($metodo->monto, 2) }} <br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <a href="{{ route('pagos.buscarpagos') }}" class="btn btn-primary">Volver</a>
        <a href="{{ route('estado.financiero.pdf', ['id' => $convenio->id]) }}" class="btn btn-danger" target="_blank">
            Descargar PDF
        </a>

    </div>
</div>

@endsection
