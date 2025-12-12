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
        <h1 class="box-title">Lista de Paquetes</h1>
    <a href="{{ route('paquetes.create') }}" class="btn btn-success mb-3">Crear Nuevo Paquete</a>
    <table id="PaquetesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Cursos Asociados</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paquetes as $index => $paquete)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $paquete->nombre }}</td>
                    <td>{{ $paquete->descripcion }}</td>
                    <td>Q.{{ number_format($paquete->precio, 2) }}</td>
                    <td>{{ $paquete->estado->estado ?? 'No asignado' }}</td>
                    <td>
                        @if ($paquete->cursos->isNotEmpty())
                            <ul>
                                @foreach ($paquete->cursos as $curso)
                                    <li>{{ $curso->curso }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span>No hay cursos asociados</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('paquetes.show', $paquete->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('paquetes.edit', $paquete->id) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('paquetes.destroy', $paquete->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este paquete?')">Eliminar</button>
                        </form>
                        <form action="{{ route('paquetes.recalcularPrecio', $paquete->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">Recalcular Precio</button>
                        </form>
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
        $('#PaquetesTable').DataTable({
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
