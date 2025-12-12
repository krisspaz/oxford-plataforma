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
        <h3>Mostrar Cuotas del Convenio</h3>
    </div>




    <div class="panel-body">
        <h4><strong>Estudiante:</strong> {{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</h4>
        <h4><strong>Paquete Seleccionado:</strong> {{ $convenio->inscripcion->paquete->nombre }}</h4>
        <h4><strong>Referencia / Convenio:</strong> {{ $convenio->id }}</h4>

        <hr>

        <h4>Detalle de las Cuotas</h4>

        <form action="{{ route('pagos.registrar') }}" method="POST">
            @csrf

            <input type="hidden" name="convenio_id" value="{{$convenio->id}}">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Producto Seleccionado</th>
                        <th>Monto Total</th>
                        <th>Fecha de Vencimiento</th>
                        <th>Exonerar</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($convenio->cuotas->where('estado', 'pendiente') as $cuota)
                        <tr>
                            <!-- Checkbox para seleccionar cuota -->
                            <td>
                                <input type="checkbox" name="cuotas[]" value="{{ $cuota->id }}" class="checkbox-cuota" data-monto="{{ $cuota->monto_cuota }}">
                            </td>
                            <!-- Nombre del producto relacionado -->
                            <td>
                                @if (strtolower($cuota->productoSeleccionado->detalle->nombre ?? '') == 'mensualidad')
                            @php
                                // Obtener el mes en español según la fecha de vencimiento
                                $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F');
                                 $anio = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y');
                            @endphp
                            Mensualidad {{ ucfirst($mes) }} {{ $anio }}
                        @else
                            {{ $cuota->productoSeleccionado->detalle->nombre ?? 'N/A' }}
                        @endif


                            </td>
                            <!-- Monto total de la cuota -->
                            <td>{{ number_format($cuota->monto_cuota, 2) }}</td>
                            <!-- Fecha de vencimiento -->
                            <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td>
                            <select name="exonerar[]" class="form-control">
                                <option value="No" selected>No</option>
                                <option value="Si">Sí</option>
                            </select>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('pagos.buscar') }}" class="btn btn-primary">Volver</a>
            <hr>


            <!-- Monto total a pagar por las cuotas seleccionadas -->
            <div class="form-group">

                <label for="monto_total">Monto Total:</label>
                <input type="text" id="monto_total" name="monto_total" class="form-control" value="{{ old('monto_total') }}" readonly>
            </div>

            <!-- Tipo de pago -->
            <div class="form-group">
                <label for="tipo_pago">Tipo de Pago:</label>
                <select id="tipo_pago" name="tipo_pago" class="form-control">
                    <option value="completo">Pago Completo</option>
                    <option value="abono">Abono</option>
                </select>
            </div>

            <!-- Selección de método de pago -->
            <div class="form-group">
                <label for="metodo_pago">Método de Pago:</label>
                <select id="metodo_pago" name="metodo_pago" class="form-control" required>
                    <option>Seleccione</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Pago con Tarjeta</option>
                    <option value="cheque">Cheque</option>
                    <option value="deposito">Deposito</option>
                    <option value="intervalo1">Efectivo y Transferencia</option>
                    <option value="intervalo2">Efectivo y Tarjeta</option>
                    <option value="intervalo3">Efectivo y Cheque</option>
                    <option value="intervalo4">Transferencia y Tarjeta</option>
                    <option value="intervalo5">Transferencia y Cheque</option>
                    <option value="intervalo6">Transferencia y Deposito</option>
                </select>
            </div>

            <!-- Campos adicionales según el tipo de pago -->
            <div id="campo_efectivo" class="form-group" style="display: none;">
                <label for="monto_efectivo">Monto en Efectivo:</label>
                <input type="number" id="monto_efectivo" name="monto_efectivo" class="form-control" step="0.01">
            </div>

            <div id="campo_transferencia" class="form-group" style="display: none;">
                <label for="monto_transferencia">Monto de la Transferencia:</label>
                <input type="text" id="monto_transferencia" name="monto_transferencia" class="form-control">
                <label for="referencia_transferencia">Número de Referencia de la Transferencia:</label>
                <input type="text" id="referencia_transferencia" name="referencia_transferencia" class="form-control">
                <label for="banco_deposito">Banco a Transferir:</label>
                <input type="text" id="banco_deposito" name="banco_deposito" class="form-control" value ="BAM 30-4019855-0">
            </div>

            <div id="campo_cheque" class="form-group" style="display: none;">
                <label for="monto_cheque">Monto (Cheque):</label>
                <input type="text" id="monto_cheque" name="monto_cheque" class="form-control">
                <label for="numero_cheque">Número de Cheque:</label>
                <input type="text" id="numero_cheque" name="numero_cheque" class="form-control">
            </div>

            <div id="campo_tarjeta" class="form-group" style="display: none;">
                <label for="monto_tarjeta">Monto (Tarjeta):</label>
                <input type="text" id="monto_tarjeta" name="monto_tarjeta" class="form-control">
                <label for="numero_baucher">Número de Baucher de Tarjeta:</label>
                <input type="text" id="numero_baucher" name="numero_baucher" class="form-control">
            </div>

            <div id="campo_deposito" class="form-group" style="display: none;">
                <label for="monto_boleta">Monto del Deposito:</label>
                <input type="text" id="monto_deposito" name="monto_deposito" class="form-control">
                <label for="nombre_deposito">Numero de Boleta:</label>
                <input type="text" id="nombre_deposito" name="no_deposito" class="form-control">
                <label for="banco_deposito">Banco a Transferir:</label>
                <input type="text" id="banco_deposito" name="banco_deposito" class="form-control" value ="BAM 30-4019855-0">
            </div>

            <!-- Botón de enviar -->
            <div class="form-group text-center">
                <button type="submit">Registrar Pago</button>
            </div>
        </form>
    </div>
</div>



@push('bottom')
    @if($errors->has('monto_total'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: '¡Monto excedido!',
                    text: "{{ $errors->first('monto_total') }}",
                    confirmButtonText: 'Aceptar',
                    width: '600px', // más ancho
                    customClass: {
                        popup: 'swal-custom-popup',
                        title: 'swal-custom-title',
                        content: 'swal-custom-content',
                    }
                });
            });
        </script>

        <style>
            .swal-custom-popup {
                font-size: 18px;
                padding: 30px;
            }

            .swal-custom-title {
                font-size: 24px;
            }

            .swal-custom-content {
                font-size: 18px;
            }
        </style>
    @endif
@endpush




@push('bottom')
    @if($errors->has('completo'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: '¡Monto Inferior en Pago Completo!',
                    text: "{{ $errors->first('completo') }}",
                    confirmButtonText: 'Aceptar',
                    width: '600px', // más ancho
                    customClass: {
                        popup: 'swal-custom-popup',
                        title: 'swal-custom-title',
                        content: 'swal-custom-content',
                    }
                });
            });
        </script>

        <style>
            .swal-custom-popup {
                font-size: 18px;
                padding: 30px;
            }

            .swal-custom-title {
                font-size: 24px;
            }

            .swal-custom-content {
                font-size: 18px;
            }
        </style>
    @endif
@endpush


<!-- Agregar el script para manejar la lógica de los campos dinámicos -->
<script>
    // Función para mostrar los campos correspondientes según el tipo de pago seleccionado
    document.getElementById('metodo_pago').addEventListener('change', function() {
        var tipoPago = this.value;

        // Ocultar todos los campos
        document.getElementById('campo_efectivo').style.display = 'none';
        document.getElementById('campo_transferencia').style.display = 'none';
        document.getElementById('campo_cheque').style.display = 'none';
        document.getElementById('campo_tarjeta').style.display = 'none';
        document.getElementById('campo_deposito').style.display = 'none';

        // Mostrar el campo correspondiente al tipo de pago seleccionado
        if (tipoPago === 'efectivo') {
            document.getElementById('campo_efectivo').style.display = 'block';
        } else if (tipoPago === 'intervalo1') {
            document.getElementById('campo_efectivo').style.display = 'block';
            document.getElementById('campo_transferencia').style.display = 'block';
        }else if (tipoPago === 'intervalo2') {
            document.getElementById('campo_efectivo').style.display = 'block';
            document.getElementById('campo_tarjeta').style.display = 'block';
        }else if (tipoPago === 'intervalo3') {
            document.getElementById('campo_efectivo').style.display = 'block';
            document.getElementById('campo_cheque').style.display = 'block';
        } else if (tipoPago === 'intervalo4') {
            document.getElementById('campo_transferencia').style.display = 'block';
            document.getElementById('campo_tarjeta').style.display = 'block';
        }else if (tipoPago === 'intervalo5') {
            document.getElementById('campo_transferencia').style.display = 'block';
            document.getElementById('campo_cheque').style.display = 'block';
        }else if (tipoPago === 'intervalo6') {
            document.getElementById('campo_transferencia').style.display = 'block';
            document.getElementById('campo_deposito').style.display = 'block';
        }else if (tipoPago === 'transferencia') {
            document.getElementById('campo_transferencia').style.display = 'block';
        } else if (tipoPago === 'cheque') {
            document.getElementById('campo_cheque').style.display = 'block';
        } else if (tipoPago === 'tarjeta') {
            document.getElementById('campo_tarjeta').style.display = 'block';
        } else if (tipoPago === 'deposito') {
            document.getElementById('campo_deposito').style.display = 'block';
        }
    });

    // Sumar el monto total al seleccionar las cuotas
    let checkboxes = document.querySelectorAll('.checkbox-cuota');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let montoTotal = 0;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    montoTotal += parseFloat(checkbox.getAttribute('data-monto'));
                }
            });
            document.getElementById('monto_total').value = montoTotal.toFixed(2);
        });
    });
</script>

@endsection
