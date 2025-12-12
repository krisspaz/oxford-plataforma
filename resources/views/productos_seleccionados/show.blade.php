@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-default">
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
            <h3 class="box-title">Productos Seleccionados para Inscripción No. Referencia: {{ $productosSeleccionados->first()->inscripcion_id }}</h3>
        </div>
        
        <div class="box-body">
            @if ($productosSeleccionados->first()->inscripcion)
                <h4>Detalles de Inscripción</h4>
                <p><strong>Nombre:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->persona->nombres ?? 'N/A' }}{{" "}}{{$productosSeleccionados->first()->inscripcion->estudiante->persona->apellidos ?? 'N/A'}}</p>
                <p><strong>Curso:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->cursos->curso ?? 'N/A' }}</p>
                <p><strong>Grado:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->grados->nombre ?? 'N/A' }}</p>
                <p><strong>Sección:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->secciones->seccion ?? 'N/A' }}</p>
                <p><strong>Fecha de Inscripción:</strong> {{ $productosSeleccionados->first()->inscripcion->fecha_inscripcion ?? 'N/A' }}</p>
            @endif

            <h4>Productos Asociados</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th> <!-- Contador secuencial -->
                        <th>Detalle</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productosSeleccionados as $index => $producto)
                        <tr>
                            <td>{{ $index + 1 }}</td> <!-- Contador que empieza desde 1 -->
                            <td>{{ $producto->detalle ? $producto->detalle->nombre : 'N/A' }}</td>
                            <td>
                               

                                @php
                                // Verificar si el nombre del producto contiene "Mensualidad"
                                $nombreProducto = strtolower(optional($producto->detalle)->nombre);
                                $precio = $producto->precio;

                                // Si es mensualidad, dividir el precio por 10
                                if (strpos($nombreProducto, 'mensualidad') !== false) {

                                  

                                        $precio = $precio ; // Dividir por 10 si es "Mensualidad"

                                    
                
                                }
                            @endphp

                            {{$precio}}


                            </td>
                            <td>
                                <form action="{{ route('productos_seleccionados.destroy', $producto) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Verificar si existen productos disponibles para añadir -->
            @if($productosDisponibles->isNotEmpty())
                <h4>Añadir Nuevo Producto</h4>
                <form action="{{ route('productos_seleccionados.store', $productosSeleccionados->first()->inscripcion_id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="producto">Seleccionar Producto</label>
                        <select name="producto_id" id="producto" class="form-control">
                            @foreach ($productosDisponibles as $producto)
                                <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Añadir Producto</button>
                </form>
            @else
                <p>No hay productos disponibles para añadir.</p>
            @endif
        </div>

        <div class="box-footer">
            <a href="{{ route('productos_seleccionados.index') }}" class="btn btn-black">Volver a la Lista</a> <!-- Cambiado a btn-black -->
            @if ($productosSeleccionados->isNotEmpty())
    <a href="{{ route('productos_seleccionados.edit', $productosSeleccionados->first()->inscripcion->id) }}" class="btn btn-warning">Editar</a>
@endif
        </div>
    </div>

    <!-- Estilos personalizados -->
    <style>
        .btn-black {
            background-color: #333030;
            color: #fff;
        }
    </style>
@endsection
