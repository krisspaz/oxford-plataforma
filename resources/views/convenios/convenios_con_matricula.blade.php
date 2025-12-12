@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <!-- Mensajes de éxito y error -->
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

    <div class="box-header">
        <h1 class="box-title">Inscripciones con Convenio</h1>
    </div>

    <div class="box-body">
        <div id="filtro-letras" style="margin-bottom: 15px;"></div>

        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>Inscripción (Referencia)</th>
                    <th>Ciclo Escolar</th>
                    <th>Estudiante</th>
                    <th>Paquete</th>
                    <th>Estado del Convenio</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Productos Seleccionados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($convenios as $estudiante => $conveniosPorEstudiante)
                    <!-- Mostrar el nombre del estudiante en una fila separada -->
                  

                    <!-- Ahora cada fila de convenio debe tener 9 columnas -->
                    @foreach($conveniosPorEstudiante as $convenio)
                        <tr data-name="{{ strtoupper(substr($convenio->inscripcion->estudiante->persona->nombres, 0, 1)) }}">
                            <td>{{ $convenio->inscripcion->id }} </td>
                            <td>{{ $convenio->inscripcion->ciclo_escolar }} </td>
                            <td>{{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</td>
                            <td>{{ $convenio->inscripcion->paquete->nombre ?? 'Sin paquete' }}</td>
                            <td>{{ ucfirst($convenio->estado) }}</td>
                            <td>{{ \Carbon\Carbon::parse($convenio->fecha_inicio)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($convenio->fecha_fin)->format('d-m-Y') }}</td>
                            <td>
                                <ul>
                                    @foreach($convenio->inscripcion->productosSeleccionados as $producto)
                                        <li>{{ $producto->detalle->nombre ?? 'Producto sin detalle' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <!-- Formulario para eliminar el convenio -->
                                <form action="{{ route('convenios.destroy', $convenio->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirmDelete()">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
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

            // Filtrado por letra (columna 2 = Estudiante)
            $('.filtro-letra').on('click', function() {
                $('.filtro-letra').removeClass('btn-info').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-info');

                let letra = $(this).data('letra');
                let regex = letra ? '^' + letra : '';
                
                // Filtrar por la primera letra del nombre del estudiante
                table.rows().every(function() {
                    var row = this.node();
                    var name = $(row).data('name');
                    
                    if (name && name.startsWith(letra)) {
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
