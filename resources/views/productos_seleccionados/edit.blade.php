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
            <h3 class="box-title">Editar Productos Seleccionados</h3>
        </div>

        <div class="box-body">
            <h4>Inscripción Referencia No.: {{ $productosSeleccionados->first()->inscripcion_id }}</h4>

            @if ($productosSeleccionados->first()->inscripcion)
                <p><strong>Nombre de la Inscripción:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->persona->nombres ?? 'N/A' }}{{" "}}{{ $productosSeleccionados->first()->inscripcion->estudiante->persona->apellidos ?? 'N/A' }}</p>
                <p><strong>Curso:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->cursos->curso ?? 'N/A' }}</p>
                <p><strong>Grado:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->grados->nombre ?? 'N/A' }}</p>
                <p><strong>Sección:</strong> {{ $productosSeleccionados->first()->inscripcion->estudiante->cgshges->secciones->seccion ?? 'N/A' }}</p>
            @endif

            <form action="{{ route('productos_seleccionados.update', $productosSeleccionados->first()->inscripcion_id) }}" method="POST">
                @csrf
                @method('PUT')

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Detalle</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productosSeleccionados as $index => $producto)
                            <tr>
                                <td>{{ $index + 1 }}</td> <!-- Contador que empieza desde 1 -->
                                <td>
                                    <input type="text" name="detalles[{{ $producto->id }}]" 
                                           value="{{ $producto->detalle ? $producto->detalle->nombre : 'N/A' }}" 
                                           class="form-control" readonly>
                                </td>
                                <td>
                                    @php
                                        // Verificar si el nombre del producto contiene "Mensualidad"
                                        $nombreProducto = strtolower(optional($producto->detalle)->nombre);
                                        $precio = $producto->precio;

                                        // Si es mensualidad, dividir el precio por 10
                                        if (strpos($nombreProducto, 'mensualidad') !== false) {

                                            if($precio>100){

                                                $precio = $precio / 10; // Dividir por 10 si es "Mensualidad"


                                            }
                                           
                                        }
                                    @endphp

                                        <input type="number" name="precios[{{ $producto->id }}]" 
                                        value="{{ $precio }}" 
                                        class="form-control" step="0.01">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="{{ route('productos_seleccionados.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@endsection
