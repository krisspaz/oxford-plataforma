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
    <h1>Productos Seleccionados</h1>

    <!-- Tabla de productos seleccionados -->
    <form action="#" method="POST">
    
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Inscripción</th>
                    <th>Detalle del Paquete</th>
                    <th>Precio</th>
                    <th>Fecha de Creación</th>
                    <th>No. de Cuotas</th>
                
                </tr>
            </thead>
            <tbody>
                @foreach ($productosSeleccionados as $productoSeleccionado)
                    @php
                        // Calcular el precio final si el nombre contiene "mensualidad" (insensible a mayúsculas/minúsculas)
                        $precioFinal = $productoSeleccionado->precio;
                        if (stripos($productoSeleccionado->detalle->nombre ?? '', 'mensualidad') !== false) {
                            $precioFinal = $productoSeleccionado->precio ;
                        }
                    @endphp
                    <tr>
                        <td>{{ $productoSeleccionado->id }}</td>
                        <td>{{ $productoSeleccionado->inscripcion->estudiante->persona->nombres ?? 'N/A' }}{{" "}}{{$productoSeleccionado->inscripcion->estudiante->persona->apellidos ?? 'N/A'}}</td>

                        <input type="hidden" name="inscripcion_id" value={{ $productoSeleccionado->inscripcion->id}}>
                        <td>{{ $productoSeleccionado->detalle->nombre ?? 'N/A' }}</td>
                        <td>{{ number_format($precioFinal, 2) }}</td>
                        <td>{{ $productoSeleccionado->created_at ? $productoSeleccionado->created_at->format('d-m-Y') : 'N/A' }}</td>
                        <td>
                            <input 
                                type="number" 
                                name="cuotas[{{ $productoSeleccionado->id }}]" 
                                class="form-control cuotas-input" 
                                data-id="{{ $productoSeleccionado->id }}" 
                                data-precio="{{ $precioFinal }}" 
                                min="1" 
                                value="1" 
                                required>
                        </td>
                       
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Campos ocultos para enviar al backend -->
        <div id="hiddenInputsContainer" nmae="oculto"></div>
       
        

        <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Crear Convenio</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cuotasInputs = document.querySelectorAll('.cuotas-input');
        const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');

        cuotasInputs.forEach(input => {
            input.addEventListener('input', () => {
                updateHiddenInput(input);
            });

            // Crear el campo oculto al cargar la página
            updateHiddenInput(input);
        });

        function updateHiddenInput(input) {
            const id = input.getAttribute('data-id');
            const precio = input.getAttribute('data-precio');
            const cuotas = input.value;

            // Buscar si ya existe un input oculto para este producto
            let existingInput = document.querySelector(`input[name="productos_seleccionados[${id}]"]`);

            if (!existingInput) {
                existingInput = document.createElement('input');
                existingInput.type = 'hidden';
                existingInput.name = `productos_seleccionados[${id}]`;
                hiddenInputsContainer.appendChild(existingInput);
            }

            // Actualizar el valor del input oculto
            existingInput.value = JSON.stringify({ id, precio, cuotas });
        }
    });
</script>
@endsection
