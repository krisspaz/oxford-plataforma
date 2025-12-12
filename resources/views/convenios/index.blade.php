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
    <div class="box-header">
        <h1 class="box-title">Listado de Inscripciones con Productos Seleccionados (Sin Gestiòn de Pagos)</h1>
    </div>
    <a href="{{ route('convenios.mostrar') }}" class="btn btn-success">
        <i class="fa fa-list"></i> Ver Convenios
    </a>
    <div class="box-body">
        <div id="filtro-letras" style="margin-bottom: 15px;"></div>
        
        <table class="table table-striped" id="datatable">
        
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Paquete</th>
                    <th>Estado</th>
                    <th>Productos Seleccionados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inscripciones as $inscripcion)
                    <tr>
                        <td>{{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}</td>
                        <td>{{ $inscripcion->paquete->nombre ?? 'Sin paquete' }}</td>
                        <td>{{ $inscripcion->estado->estado ?? 'Sin estado' }}</td>
                        <td>
                            <ul>
                                @foreach($inscripcion->productosSeleccionados as $producto)
                                    <li>{{ $producto->detalle->nombre ?? 'Producto sin detalle' }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <a href="{{ route('convenios.create', $inscripcion->id) }}" class="btn btn-xs btn-success">
                                <i class="fa fa-plus"></i> Asignar Convenio
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('bottom')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm('¿Estás seguro de que quieres eliminar este convenio y sus detalles?');
        }

        $(document).ready(function() {
            const table = $('#datatable').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles en la tabla",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                    infoFiltered: "(filtrado de _MAX_ entradas totales)",
                    thousands: ",",
                    lengthMenu: "Mostrar _MENU_ entradas",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna ascendente",
                        sortDescending: ": activar para ordenar la columna descendente"
                    }
                }
            });

            // Crear botones A-Z + Todos
            const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            const contenedor = $('#filtro-letras');
            contenedor.append(`<button class="btn btn-info filtro-letra" data-letra="">Todos</button> `);
            letras.forEach(function(letra) {
                contenedor.append(`<button class="btn btn-default filtro-letra" data-letra="${letra}">${letra}</button> `);
            });

            // Filtrado por letra (columna 1 = Estudiante)
            $('.filtro-letra').on('click', function() {
                $('.filtro-letra').removeClass('btn-info').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-info');

                let letra = $(this).data('letra');
                let regex = letra ? '^' + letra : '';
                
                // Filtrar por la primera letra del nombre del estudiante
                table.rows().every(function() {
                    var row = this.node();
                    var name = $(row).find('td:first').text().trim().charAt(0).toUpperCase(); // Obtener la primera letra del nombre
                    
                    if (name && name.startsWith(letra.toUpperCase())) {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            });
        });
    </script>
@endpush
@endsection
