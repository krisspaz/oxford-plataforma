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
        <h3>Convenios Sin Gestión de Cuotas</h3>
    </div>

    <div class="mb-3">
        <label for="buscarEstudiante">Buscar estudiante:</label>
        <input type="text" id="buscarEstudiante" class="form-control" placeholder="Escriba el nombre del estudiante...">
    </div>
    <br>
    <a href="{{ route('convenios.con_detalles') }}" class="btn btn-primary">Ver Convenios con Detalles</a>
    <div class="panel-body">
        <div id="filtro-letras" style="margin-bottom: 15px;"></div>
        
        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Paquete</th>
                    <th>Fecha de Inscripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($conveniosSinDetalles as $convenio)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $convenio->inscripcion->estudiante->persona->nombres ?? 'N/A' }} {{ $convenio->inscripcion->estudiante->persona->apellidos ?? '' }}</td>
                        <td>{{ $convenio->inscripcion->paquete->nombre ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($convenio->inscripcion->fecha_inscripcion)->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('convenios_detalles.create', $convenio->id) }}" class="btn btn-primary">
                                Añadir Detalles
                            </a>
                        </td>
                    </tr>
                @empty
                 
                @endforelse
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

            // Filtrado por letra (columna 2 = Estudiante)
            $('.filtro-letra').on('click', function() {
                $('.filtro-letra').removeClass('btn-info').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-info');

                let letra = $(this).data('letra');
                let regex = letra ? '^' + letra : '';
                
                // Filtrar por la primera letra del nombre del estudiante
                table.rows().every(function() {
                    var row = this.node();
                    var name = $(row).find('td:nth-child(2)').text().trim().charAt(0).toUpperCase(); // Obtener la primera letra del nombre
                    
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
