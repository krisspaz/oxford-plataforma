@extends('crudbooster::admin_template')

@section('content')

<div class="panel panel-default">
    <div class="panel-heading d-flex justify-content-between align-items-center">
        <h3>Estudiantes Con Solicitud de Exoneración por Motivo de Baja o Retiro</h3>


    </div>

    <div class="panel-body">

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- ===================================================== --}}
        {{-- =============== BUSCADOR DE ESTUDIANTE =========== --}}
        {{-- ===================================================== --}}
        <form method="GET" action="{{ url()->current() }}" class="mb-3" style="margin-bottom:20px;">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar estudiante por nombre o apellido...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Buscar</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ url()->current() }}" class="btn btn-default">Limpiar</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="bg-primary" style="color:white;">
                <tr>
                    <th style="width: 25%">Estudiante</th>
                    <th style="width: 15%">Ciclos</th>
                    <th style="width: 15%">Monto Total (Q)</th>
                    <th style="width: 45%">Detalle de Convenios y Cuotas Pendientes</th>
                    <th style="width: 20%">Acción</th>
                </tr>
            </thead>

            <tbody>

            @foreach($resultados as $index => $item)
                @php
                    $estudiante = $item['estudiante'];
                    $ciclos = $item['ciclos'] ?? [];
                    $convenios = $item['convenios'] ?? [];

                    $montoTotal = 0;
                    foreach($convenios as $info) {
                        if (!empty($info['cuotas'])) {
                            foreach ($info['cuotas'] as $c) {
                                $montoTotal += floatval($c->monto_cuota ?? ($c->monto ?? 0));
                            }
                        }
                    }

                    $collapseId = 'detalle-' . $index;
                @endphp

                <tr>
                    <!-- ESTUDIANTE -->
                    <td>
                        <strong>{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</strong><br>
                        <small>Carné: {{ $estudiante->carnet ?? $estudiante->carne ?? 'N/A' }}</small>
                    </td>

                    <!-- CICLOS -->
                    <td>
                        @if(!empty($ciclos))
                            @foreach(array_unique($ciclos) as $ciclo)
                                <span class="label label-info" style="display:block; margin-bottom:5px;">{{ $ciclo }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Sin ciclos</span>
                        @endif
                    </td>

                    <!-- MONTO TOTAL -->
                    <td class="text-right" style="font-weight:700;">Q {{ number_format($montoTotal, 2) }}</td>

                    <!-- DETALLE -->
                    <td>
                        <button class="btn btn-sm btn-info" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false">
                            Ver detalles
                        </button>

                        <div id="{{ $collapseId }}" class="collapse mt-3">

                            @php
                                $totalesCiclo = [];
                                foreach($convenios as $info) {
                                    $matricula = $info['matricula'];
                                    $cuotas = $info['cuotas'] ?? collect();
                                    $ciclo = $matricula->ciclo_escolar;
                                    if (!isset($totalesCiclo[$ciclo])) $totalesCiclo[$ciclo] = 0;
                                    foreach ($cuotas as $c) $totalesCiclo[$ciclo] += floatval($c->monto_cuota ?? ($c->monto ?? 0));
                                }
                            @endphp

                            @if(!empty($totalesCiclo))
                                <h4><strong>Totales por Ciclo Escolar</strong></h4>
                                <table class="table table-bordered table-sm" style="background:#fcfcfc;">
                                    <thead>
                                        <tr>
                                            <th style="width:150px;">Ciclo Escolar</th>
                                            <th style="width:150px;" class="text-right">Monto Total (Q)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($totalesCiclo as $ciclo => $total)
                                            <tr>
                                                <td>{{ $ciclo }}</td>
                                                <td class="text-right" style="font-weight:700;">Q {{ number_format($total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Detalle de convenios y cuotas --}}
                            @if(!empty($convenios))
                                @foreach($convenios as $info)
                                    @php
                                        $convenio = $info['convenio'];
                                        $matricula = $info['matricula'];
                                        $cuotas = $info['cuotas'] ?? collect();
                                    @endphp

                                    <div style="padding:10px; border:1px solid #ddd; margin-bottom:10px; border-radius:5px;">

                                        <div class="row">
                                            <div class="col-md-8">
                                                <strong>Convenio:</strong> {{ $convenio->id }} <br>
                                                <strong>Matrícula:</strong> Ciclo {{ $matricula->ciclo_escolar }}
                                            </div>

                                            <div class="col-md-4 text-right">
                                                <form method="POST" action="{{ route('pagos.buscar') }}">
                                                    @csrf
                                                    <input type="hidden" name="criterio" value="estudiante">
                                                    <input type="hidden" name="valor" value="{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}">
                                                    <input type="hidden" name="ciclo_escolar" value="{{ $matricula->ciclo_escolar }}">
                                                    <button type="submit" class="btn btn-xs btn-primary">Registrar Pago</button>
                                                </form>
                                            </div>
                                        </div>

                                        <table class="table table-condensed table-bordered" style="margin-top:10px;">
                                            <thead>
                                                <tr style="background:#f7f7f7;">
                                                    <th>Producto</th>
                                                    <th style="width:120px">Monto (Q)</th>
                                                    <th style="width:120px">Vencimiento</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($cuotas as $cuota)
                                                    @php
                                                        $detalleNombre = optional(optional($cuota->productoSeleccionado)->detalle)->nombre;
                                                        $nombreProducto = $detalleNombre ? ucfirst($detalleNombre) : 'N/A';

                                                        try {
                                                            $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F');
                                                            $anio = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y');
                                                        } catch (\Exception $e) {
                                                            $mes = '';
                                                            $anio = '';
                                                        }

                                                        $esMensualidad = strtolower($detalleNombre ?? '') === 'mensualidad';
                                                        $montoCuota = floatval($cuota->monto_cuota ?? ($cuota->monto ?? 0));
                                                    @endphp

                                                    <tr>
                                                        <td>
                                                            @if($esMensualidad && $mes)
                                                                {{ $nombreProducto }} {{ ucfirst($mes) }} {{ $anio }}
                                                            @else
                                                                {{ $nombreProducto }}
                                                            @endif
                                                        </td>
                                                        <td class="text-right">Q {{ number_format($montoCuota, 2) }}</td>
                                                        <td>
                                                            @if(!empty($cuota->fecha_vencimiento))
                                                                {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">No hay cuotas pendientes en este convenio.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </td>

                    <!-- ACCIONES -->
                    <td>

                        {{-- EXONERAR CUOTAS --}}
                        <a href="{{ route('cuotas.exonerar.procesar', $estudiante->id) }}"
                           class="btn btn-success btn-sm"
                           onclick="return confirm('¿Estás apunto de Exonerar cuotas Pendientes para seleccionar la cuota o las cuotas Pendientes presiona Aceptar?');">
                            Exonerar
                        </a>

                         <br><br>

                         {{-- EXONERAR CUOTAS --}}
                        <a href="{{ route('cuotas.exoneradas', $estudiante->id) }}"
                           class="btn btn-warning btn-sm"
                           onclick="return confirm('Para ver las cuotas exoneradas y rechazarlas presiona Aceptar');">
                            Rechazar
                        </a>


                        </form>

                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection
