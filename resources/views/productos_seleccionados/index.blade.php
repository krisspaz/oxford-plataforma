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
        <h3>Listado de Inscripciones y Paquetes Seleccionados</h3>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<div class="box-body">


    <table id="InscripcionesPaquetesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Ciclo Escolar</th>
                <th>Referecia No.</th>
                <th>Estudiante</th>
                <th>Cantidad de Productos</th>
                <th>Total Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumenInscripciones as $resumen)
                <tr>
                    <td>{{ $resumen->inscripcion->ciclo_escolar }}</td>
                    <td>{{ $resumen->inscripcion->id }}</td>
                    <td>{{ $resumen->inscripcion->estudiante->persona->nombres }}{{" "}}{{ $resumen->inscripcion->estudiante->persona->apellidos }}</td>
                    <td>{{ $resumen->cantidad_productos }}</td>
                    <td>Q. {{ number_format($resumen->total_precio, 2) }}</td>
                    <td>
                        <a href="{{ route('productos_seleccionados.show', $resumen->inscripcion_id) }}" class="btn btn-info">Ver</a>
                        <a href="{{ route('productos_seleccionados.edit', $resumen->inscripcion_id) }}" class="btn btn-warning">Editar</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

@push('bottom')


<script>
    $(document).ready(function() {
        $('#InscripcionesPaquetesTable').DataTable({
            "searching": true,
            "ordering": true,
            "pageLength": 10,
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
    });
</script>

@endpush
@endsection
