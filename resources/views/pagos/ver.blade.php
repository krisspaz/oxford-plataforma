@extends('crudbooster::admin_template')

@section('content')
<div class="box">

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
    <div class="box-header with-border">
        <h3 class="box-title">Pagos Realizados</h3>
    </div>
    <div class="box-body">
        @foreach ($datosPagos as $pago)
            @if ($loop->first) <!-- Solo mostrar una vez los datos de la inscripción -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                      
                    </div>
                    <div class="panel-body">
                       
                       
                    </div>
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>
                        <strong>Pago #{{ $pago['id'] }}</strong>
                        <p><strong>Estudiante:</strong> {{ $pago['inscripcion']['estudiante']['persona']->nombres }} {{ $pago['inscripcion']['estudiante']->persona->apellidos }}</p>
                        <p><strong>Curso:</strong> {{ $pago['inscripcion']['cgshges']->cursos->curso }}</p>
                        <p><strong>Grado:</strong> {{ $pago['inscripcion']['cgshges']->grados->nombre }}</p>
                        <p><strong>Sección:</strong> {{ $pago['inscripcion']['cgshges']->secciones->seccion }}</p>
                        <span class="pull-right">
                            <strong>Monto Total:</strong> Q. {{ number_format($pago['monto'], 2) }}
                        </span>
                    </h4>
                    <p>
                        <strong>Convenio ID:</strong> {{ $pago['convenio_id'] }} | 
                        <strong>Fecha:</strong> {{ $pago['fecha_pago'] }} | 
                        <strong>Tipo de Pago:</strong> {{ $pago['tipo_pago'] }}
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <!-- Cuotas -->
                        <div class="col-md-6">
                            <h5><strong>Cuotas Asociadas:</strong></h5>
                            <ul class="list-group">
                                @foreach ($pago['cuotas'] as $cuota)
                                    <li class="list-group-item">
                                        <strong>Cuota #{{ $cuota['id'] }}</strong><br>
                                        <strong>Monto Restante:</strong> Q. {{ number_format($cuota['monto_cuota'], 2) }}<br>
                                        <strong>Estado:</strong> 
                                        <span class="label {{ $cuota['estado'] == 'pagada' ? 'label-success' : 'label-warning' }}">
                                            {{ ucfirst($cuota['estado']) }}
                                        </span>
                                        @if ($cuota['producto_seleccionado'])
                                            <br>
                                            <strong>Producto:</strong> {{ $cuota['producto_seleccionado']['nombre'] }}<br>
                                            <strong>Precio:</strong> Q. {{ number_format($cuota['producto_seleccionado']['precio'], 2) }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Métodos de Pago -->
                        <div class="col-md-6">
                            <h5><strong>Métodos de Pago:</strong></h5>
                            <ul class="list-group">
                                @foreach ($pago['metodos_de_pago'] as $metodo)
                                    <li class="list-group-item">
                                        <strong>Método:</strong> {{ ucfirst($metodo['metodo']) }}<br>
                                        <strong>Monto:</strong> Q. {{ number_format($metodo['monto'], 2) }}
                                        @if (!empty($metodo['detalles']))
                                            <br>
                                            <small>
                                                @foreach ($metodo['detalles'] as $key => $value)
                                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                                                @endforeach
                                            </small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>



                </div>
            </div>
        @endforeach
         <form method="POST" action="{{ route('pagos.generarPago') }}">
            @csrf
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Seleccionar Pagos</strong>
                </div>
                <div class="panel-body">
                    <!-- Selección de Pagos -->
                    <div class="checkbox">
                        @foreach ($datosPagos as $pago)
                        <label>
                            <input type="checkbox" name="pagos[]" value="{{ $pago['id'] }}">
                            Pago #{{ $pago['id'] }} - {{ $pago['inscripcion']['estudiante']['persona']->nombres }} 
                            {{ $pago['inscripcion']['estudiante']->persona->apellidos }}
                            
                            @if (!empty($pago['cuotas']))
                                @foreach ($pago['cuotas'] as $cuota)
                                    - {{ $cuota['producto_seleccionado']['nombre'] }}
                                @endforeach
                            @endif
                    
                            @if (!empty($pago['metodos_de_pago']))
                                @foreach ($pago['metodos_de_pago'] as $metodo)
                                    - Monto: {{ $metodo['monto'] }}
                                @endforeach
                            @endif
                        </label>
                        <br>
                    @endforeach
                    
                    </div>

                    <!-- Acción para Generar Factura, Recibo, o Recibo Interno -->
                    <div class="form-group">
                        <label for="accion">Seleccione una acción:</label>
                        <select name="accion" id="accion" class="form-control">
                            <option value="generarFactura">Generar Factura</option>
                            <option value="generarRecibo">Generar Recibo</option>
                            <option value="generarReciboInterno">Generar Recibo Interno</option>
                        </select>
                    </div>
                </div>

                <!-- Botón de Enviar -->
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary">Generar Pago</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
