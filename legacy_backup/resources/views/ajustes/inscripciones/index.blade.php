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
        <h3 class="box-title">Listado de Inscripciones</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('ajustes_inscripciones.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Nueva Inscripción
            </a>
        </div>
    </div>

    <div class="box-body">
        <div id="filtro-letras" style="margin-bottom: 15px;"></div>

        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Jornada</th>
                    <th>Paquete</th>
                    <th>Fecha de Inscripción</th>
                    <th>Ciclo Escolar</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inscripciones as $inscripcion)
                    <tr>
                        <td>{{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}</td>
                        <td>{{ $inscripcion->cgshges->grados->nombre }}{{ $inscripcion->cgshges->cursos->curso }}</td>
                        <td>{{ $inscripcion->cgshges->secciones->seccion }}</td>
                        <td>{{ $inscripcion->cgshges->jornadas->jornada->nombre }}</td>
                        <td>{{ $inscripcion->paquete->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y') }}</td>
                        <td>{{ $inscripcion->ciclo_escolar }}</td>
                        <td>{{ $inscripcion->estado->estado }}</td>
                        <td>
                            <a href="{{ route('ajustes_inscripciones.show', $inscripcion->id) }}" class="btn btn-primary btn-xs">
                                <i class="fa fa-eye"></i> Ver
                            </a>
                            <a href="{{ route('ajustes_inscripciones.edit', $inscripcion->id) }}" class="btn btn-warning btn-xs">
                                <i class="fa fa-pencil"></i> Editar
                            </a>
                            <form action="{{ route('ajustes_inscripciones.destroy', $inscripcion->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Estás seguro de eliminar esta inscripción?')">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </form>
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
        $(document).ready(function() {
            const table = $('#datatable').DataTable({
                "searching": true,
            "ordering": true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
    pageLength: 100,
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sPrevious": "Anterior",
                    "sNext":     "Siguiente",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
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

            // Filtrado por letra (columna 0 = Estudiante)
            $('.filtro-letra').on('click', function() {
                $('.filtro-letra').removeClass('btn-info').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-info');

                let letra = $(this).data('letra');
                let regex = letra ? '^' + letra : '';
                table.column(0).search(regex, true, false).draw();
            });
        });
    </script>
@endpush

@endsection
