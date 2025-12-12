@extends('layouts.app')

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
<div class="container">
    <h2>Detalles 2 de la Inscripción</h2>
    <div class="card">
        <div class="card-header">
            <h3>Estudiante: {{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}</h3>
        </div>
        <form action="{{ route('convenios.store') }}" method="POST">
            @csrf

    
            
            <input type="hidden" name="estudiante_id" value="{{ $inscripcion->estudiante->id }}">
            <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">
            <input type="hidden" id="productosSeleccionados" name="productos">

            <div class="card-body">
                <!-- Información del paquete -->
                <h4>Paquete Seleccionado: {{ $inscripcion->paquete->nombre }}</h4>
                <p><strong>Descripción del Paquete:</strong> {{ $inscripcion->paquete->descripcion }}</p>
                <p><strong>Fecha de Inscripción:</strong> {{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d-m-Y') }}</p>
                <p><strong>Estado:</strong> {{ $inscripcion->estado->estado }}</p>
                <p><strong>Usuario que gestionó la inscripción:</strong> {{ $inscripcion->cmsUser->name }}</p>

                <hr>

                <!-- Seleccionar detalles del paquete -->
                <h5>Detalles del Paquete:</h5>
                <div>
                    <input type="checkbox" id="seleccionarTodos">
                    <label for="seleccionarTodos"><strong>Seleccionar Todos</strong></label>
                </div>
                @php
                    $montoTotal = 0;
                @endphp
                <div id="detallesPaquete">
                    @foreach ($inscripcion->paquete->detalles as $detalle)
                        <div class="card mb-3 detalle-paquete" data-id="{{ $detalle->id }}" data-precio="{{ $detalle->precio }}">
                            <div class="card-body">
                                <input type="checkbox" class="seleccionar-detalle" 
                                       data-id="{{ $detalle->id }}" 
                                       data-nombre="{{ $detalle->nombre }}" 
                                       data-precio="{{ $detalle->precio }}">
                                <strong>Detalle:</strong> {{ $detalle->nombre }} <br>
                                <strong>Descripción:</strong> {{ $detalle->descripcion }} <br>
                                <strong>Precio Unitario:</strong> {{ number_format($detalle->precio, 2) }} <br>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>

                <!-- Productos asociados al paquete -->
                <h5>Productos Asociados al Paquete:</h5>
                @forelse ($inscripcion->paquete->detalles as $detalle)
                    @foreach ($detalle->detallesProductos as $detalleProducto)
                        <div class="card mb-3">
                            <div class="card-body">
                                <strong>Paquete:</strong> {{ $detalleProducto->paqueteDetalle->nombre }} <br>
                                <strong>Producto:</strong> {{ $detalleProducto->producto->nombre }} <br>
                                <strong>Descripción:</strong> {{ $detalleProducto->producto->descripcion }} <br>
                                <strong>Cantidad:</strong> {{ $detalleProducto->cantidad }} <br>
                                <strong>Precio Total Producto:</strong> {{ number_format($detalleProducto->producto->precio * $detalleProducto->cantidad, 2) }} <br>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <p>No hay productos asociados a este paquete.</p>
                @endforelse

                <hr>

                <!-- Monto total -->
                <h5><strong>Monto Total:</strong> <span id="montoTotal">0.00</span></h5>

                <!-- Botón guardar -->
                <button type="submit" class="btn btn-success">Guardar Convenio</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let montoTotal = 0;
        let detallesSeleccionados = [];

        function actualizarMonto() {
            montoTotal = detallesSeleccionados.reduce((total, detalle) => total + detalle.precio, 0);
            document.getElementById('montoTotal').innerText = montoTotal.toFixed(2);

            // Actualizar el input oculto con JSON
            document.getElementById('productosSeleccionados').value = JSON.stringify(detallesSeleccionados);
        }

        const seleccionarTodosCheckbox = document.getElementById('seleccionarTodos');
        const detallesCheckboxes = document.querySelectorAll('.seleccionar-detalle');

        seleccionarTodosCheckbox.addEventListener('change', function () {
            detallesSeleccionados = [];
            detallesCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;

                const detalle = {
                    id: checkbox.dataset.id,
                    nombre: checkbox.dataset.nombre,
                    precio: parseFloat(checkbox.dataset.precio)
                };

                if (this.checked) detallesSeleccionados.push(detalle);
            });

            actualizarMonto();
        });

        detallesCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const detalle = {
                    id: this.dataset.id,
                    nombre: this.dataset.nombre,
                    precio: parseFloat(this.dataset.precio)
                };

                if (this.checked) {
                    detallesSeleccionados.push(detalle);
                } else {
                    detallesSeleccionados = detallesSeleccionados.filter(d => d.id !== detalle.id);
                    seleccionarTodosCheckbox.checked = false;
                }

                if ([...detallesCheckboxes].every(chk => chk.checked)) {
                    seleccionarTodosCheckbox.checked = true;
                }

                actualizarMonto();
            });
        });

        actualizarMonto();
    });
</script>
@endsection
