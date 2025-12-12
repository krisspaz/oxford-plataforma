@extends('crudbooster::admin_template')

@section('content')
    <p><a href="{{ route('ajuste-familiar.create') }}" class="btn btn-success">Agregar Familia</a></p>

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

        <div class="box-header with-border">
            <h3 class="box-title">Listado de Familias</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered datatable-familias">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Familiar</th>
                        <th>Código</th>
                        <th>Padre</th>
                        <th>Madre</th>
                        <th>Encargado</th>
                        <th>Estudiantes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($familias->groupBy('codigo_familiar') as $codigo => $grupo)
                        @php $primero = $grupo->first(); @endphp
                        <tr>
                            <td>{{ $primero->id }}</td>
                            <td>{{ $primero->nombre_familiar }}</td>
                            <td>{{ $codigo }}</td>
                            <td>{{ $primero->padre->nombres ?? '—' }} {{ $primero->padre->apellidos ?? '—' }}</td>
                            <td>{{ $primero->madre->nombres ?? '—' }} {{ $primero->madre->apellidos ?? '—' }}</td>
                            <td>{{ $primero->encargado->nombres ?? '—' }} {{ $primero->encargado->apellidos ?? '—' }}</td>
                            <td>
                                <ul style="padding-left: 15px; margin:0;">
                                    @foreach($grupo as $item)
                                        <li>📚{{ $item->estudiante->persona->nombres ?? '—' }} {{ $item->estudiante->persona->apellidos ?? '—' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $primero->estado->estado ?? '—' }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('ajuste-familiar.show', $primero->id) }}">Ver</a>
                                <a class="btn btn-xs btn-warning" href="{{ route('ajuste-familiar.edit', $primero->id) }}">Editar</a>
                                <form method="POST" action="{{ route('ajuste-familiar.destroy', $primero->id) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('¿Estás seguro?')" class="btn btn-xs btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.datatable-familias').DataTable({
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
        });
    </script>
@endsection
