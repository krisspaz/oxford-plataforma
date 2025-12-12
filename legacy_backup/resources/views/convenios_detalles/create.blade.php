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
        <h3>Registrar Detalles del Convenio</h3>
    </div>

    @if ($errors->any())
<div>
<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
</div>
@endif
    <div class="panel-body">
        <form action="{{ route('convenios_detalles.store') }}" method="POST">
            @csrf
            <input type="hidden" name="convenio_id" value="{{ $convenio->id }}">

            <h4><strong>Estudiante:</strong> {{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</h4>
            <h4><strong>Paquete Seleccionado:</strong> {{ $convenio->inscripcion->paquete->nombre }}</h4>

            <hr>

            <div class="form-group">
                <label for="fecha_inicio"><strong>Fecha de Vencimiento de la Primera Cuota:</strong></label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
            </div>

            <h4>Productos Seleccionados</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>
                            Cantidad
                            <input type="checkbox" id="toggleEditCantidad" /> Habilitar Edición
                        </th>
                        <th>Monto Total</th>
                        <th>Número de Cuotas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($convenio->inscripcion->productosSeleccionados as $productoSeleccionado)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $productoSeleccionado->detalle->nombre }}</td>
                            <td>{{ $productoSeleccionado->detalle->descripcion }}</td>
                            <td>
                                  {{ number_format($productoSeleccionado->precio * (strtolower($productoSeleccionado->detalle->nombre) == 'mensualidad' ? 1 : 1), 2) }}

                            </td>
                            <td>
                                <!-- Si el nombre del producto es "Mensualidad" o "mensualidad", asigna 10, en caso contrario asigna 1 -->
                                <input type="number" name="cantidad[{{ $productoSeleccionado->id }}]" class="form-control cantidad" min="1" value="{{ strtolower($productoSeleccionado->detalle->nombre) == 'mensualidad' ? 1 : 1 }}" placeholder="1" disabled>
                            </td>
                            <td>
                                <!-- Monto Total se calcula multiplicando el precio unitario por la cantidad -->
                                <span class="monto-total" data-precio="{{ $productoSeleccionado->precio }}">
                                    {{ number_format($productoSeleccionado->precio * (strtolower($productoSeleccionado->detalle->nombre) == 'mensualidad' ? 1 : 1), 2) }}
                                </span>

                                <!-- Campo oculto para enviar el monto total al servidor -->
                                <input type="hidden" name="monto_total[{{ $productoSeleccionado->id }}]" class="monto-total-input" value="{{ $productoSeleccionado->precio * (strtolower($productoSeleccionado->detalle->nombre) == 'mensualidad' ? 1 : 1) }}">
                            </td>
                            <td>
                                <input type="number" name="cuotas[{{ $productoSeleccionado->id }}]" class="form-control" min="1" placeholder="Cuotas" value="{{  (strtolower($productoSeleccionado->detalle->nombre) == 'mensualidad' ? 10 : 1) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Guardar Detalles</button>
        </form>
    </div>
</div>

<script>
    // Cuando se cambie el estado del checkbox, habilitar o deshabilitar las celdas de cantidad
    document.getElementById('toggleEditCantidad').addEventListener('change', function() {
        const cantidadInputs = document.querySelectorAll('.cantidad');
        const isChecked = this.checked;

        cantidadInputs.forEach(function(input) {
            // Si el checkbox está marcado, habilita la edición
            input.disabled = !isChecked;
        });
    });

    // Actualizar el monto total cuando se cambie la cantidad
    document.querySelectorAll('.cantidad').forEach(function(input) {
        input.addEventListener('input', function() {
            const row = input.closest('tr');
            const cantidad = parseFloat(input.value) || 0;
            const precioUnitario = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(/[^0-9.-]+/g, "")); // Precio Unitario
            const montoTotal = row.querySelector('.monto-total');
            const montoTotalInput = row.querySelector('.monto-total-input');

            // Calcular y actualizar el Monto Total
            const total = cantidad * precioUnitario;
            montoTotal.textContent = total.toFixed(2); // Actualizar Monto Total en la columna

            // Actualizar el campo oculto para el monto total
            montoTotalInput.value = total.toFixed(2);
        });
    });
</script>
@endsection
