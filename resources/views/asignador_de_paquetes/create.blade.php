@extends('crudbooster::admin_template')

@section('content')
    <div class="panel panel-default">

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
        <div class="panel-heading">
            <h3>Detalles de la Inscripción</h3>
        </div>

         @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ( Session::get('message') != '' )
<div class='alert alert-warning'>
    {{ Session::get('message') }}
</div>
@endif
        <div class="panel-body">
            <form action="{{ route('asignador_de_paquetes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="estudiante_id" value="{{ $inscripcion->estudiante->id }}">
                <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">
                <input type="hidden" id="productosSeleccionados" name="productos">

                <div class="row">
                    <div class="col-md-6">
                        <h4><strong>Estudiante:</strong> {{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}</h4>
                        <p><strong>Fecha de Inscripción:</strong> {{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d-m-Y') }}</p>
                        <p><strong>Estado:</strong> {{ $inscripcion->estado->estado }}</p>
                        <p><strong>Usuario que gestionó la inscripción:</strong> {{ $inscripcion->cmsUser->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h4><strong>Paquete Seleccionado:</strong> {{ $inscripcion->paquete->nombre }}</h4>
                        <p><strong>Descripción del Paquete:</strong> {{ $inscripcion->paquete->descripcion }}</p>
                    </div>
                </div>

                <hr>

                <h4>Detalles del Paquete:</h4>
                <div>
                    <input type="checkbox" id="seleccionarTodos">
                    <label for="seleccionarTodos"><strong>Seleccionar Todos</strong></label>
                </div>
                @php
                    $montoTotal = 0;
                @endphp
                <div id="detallesPaquete" class="row">
                    @if ($inscripcion->paquete && $inscripcion->paquete->detalles)
                    @foreach ($inscripcion->paquete->detalles as $detalle)
                        <div class="col-md-4">
                            <div class="box box-primary detalle-paquete" data-id="{{ $detalle->id }}" data-precio="{{ $detalle->precio }}">
                                <div class="box-body">
                                    <input type="checkbox" class="seleccionar-detalle" 
                                           data-id="{{ $detalle->id }}" 
                                           data-nombre="{{ $detalle->nombre }}" 
                                           data-precio="{{ $detalle->precio }}">
                                    <strong>Detalle:</strong> {{ $detalle->nombre }} <br>
                                    <strong>Descripción:</strong> {{ $detalle->descripcion }} <br>
                                    <strong>Precio Unitario:</strong> {{ number_format($detalle->precio, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>

                <hr>

                <h4>Productos Asociados al Paquete:</h4>
                <div class="row">
                    @forelse ($inscripcion->paquete->detalles as $detalle)
                        @foreach ($detalle->detallesProductos as $detalleProducto)
                            <div class="col-md-4">
                                <div class="box box-info">
                                    <div class="box-body">
                                        <strong>Nombre del Paquete:</strong> {{ $detalle->nombre }} <br>
                                        <strong>Producto:</strong> {{ $detalleProducto->producto->nombre }} <br>
                                        <strong>Descripción:</strong> {{ $detalleProducto->producto->descripcion }} <br>
                                        <strong>Cantidad:</strong> {{ $detalleProducto->cantidad }} <br>
                                        <strong>Precio Total Producto:</strong> {{ number_format($detalleProducto->producto->precio * $detalleProducto->cantidad, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <p>No hay productos asociados a este paquete.</p>
                    @endforelse
                </div>

                <hr>

                <h4><strong>Monto Total:</strong> <span id="montoTotal">0.00</span></h4>

                <button type="submit" class="btn btn-success">Guardar Convenio</button>
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
    
                    // Multiplicar el precio por 10 si el nombre contiene "mensualidad"
                    if (detalle.nombre.toLowerCase().includes('mensualidad')) {
                        detalle.precio *= 10;
                    }
    
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
    
                    // Multiplicar el precio por 10 si el nombre contiene "mensualidad"
                    if (detalle.nombre.toLowerCase().includes('mensualidad')) {
                        detalle.precio *= 10;
                    }
    
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
