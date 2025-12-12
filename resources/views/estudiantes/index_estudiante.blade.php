@extends('crudbooster::admin_template')

@section('title', 'Mis Tareas')

@section('content')
    <div class="box">
        {{-- SweetAlert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Notificaciones --}}
        @if (session('success'))
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

        @if (session('error'))
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
            <h3 class="box-title">Mis Tareas</h3>
            <a href="{{ route('estudiantes.tareas.panelMaterias') }}" class="btn btn-sm btn-info pull-right">Ir al Panel de Materias</a>
        </div>

        <div class="box-body">
            <br>

             <!-- Filtro por Bimestre -->
           <div class="mb-3">
            <a href="{{ url()->current() }}" class="btn btn-secondary">Todos</a>
            <a href="{{ url()->current() }}?bimestre=Bimestre I" class="btn btn-info">Bimestre I</a>
            <a href="{{ url()->current() }}?bimestre=Bimestre II" class="btn btn-info">Bimestre II</a>
            <a href="{{ url()->current() }}?bimestre=Bimestre III" class="btn btn-info">Bimestre III</a>
            <a href="{{ url()->current() }}?bimestre=Bimestre IV" class="btn btn-info">Bimestre IV</a>
        </div>

        <br>


            {{-- Tabla de tareas --}}
            <table id="TareasTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Bimestre</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Tarea</th>
                        <th>Descripción</th>
                        <th>Valor</th>
                        <th>Fecha de Entrega</th>
                        <th>Fecha Entregada</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tareas as $tareaEstudiante)
                        <tr>
                            <td>{{ $tareaEstudiante->tarea->bimestre->nombre }}</td>
                            <td>{{ $tareaEstudiante->tarea->materia->gestionMateria->nombre }}</td>
                            <td>{{ $tareaEstudiante->tarea->docente->persona->nombres }} {{ $tareaEstudiante->tarea->docente->persona->apellidos }}</td>
                            <td>{{ $tareaEstudiante->tarea->titulo }}</td>
                            <td>{{ $tareaEstudiante->tarea->descripcion }}</td>
                            <td>{{ $tareaEstudiante->tarea->punteo }}</td>
                            <td>
                                {{ $tareaEstudiante->tarea->fexpiracion
                                    ? \Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion)->format('d/m/Y')
                                    : 'No disponible' }}
                            </td>
                            <td>
                                {{ $tareaEstudiante->fecha_entrega
                                    ? \Carbon\Carbon::parse($tareaEstudiante->fecha_entrega)->format('d/m/Y')
                                    : 'No disponible' }}
                            </td>
                            <td>{{ ucfirst($tareaEstudiante->estado) }}</td>
                            <td>
                                {{-- Subir tarea si está pendiente y dentro del tiempo --}}
                                @if($tareaEstudiante->estado == 'pendiente' &&
                                    (\Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion)->isFuture() ||
                                    \Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion)->isToday()))
                                    <form action="{{ route('estudiantes.tareas.upload', $tareaEstudiante->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="archivo" class="form-control" required>
                                        <button type="submit" class="btn btn-sm btn-primary mt-2">
                                            <i class="fa fa-upload"></i> Subir Tarea
                                        </button>
                                    </form>
                                @elseif($tareaEstudiante->estado == 'pendiente')
                                    <span class="badge badge-danger">Fecha límite vencida</span>
                                @elseif($tareaEstudiante->estado == 'entregada')
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('estudiantes.tareas.descargar', $tareaEstudiante->id) }}" class="btn btn-sm btn-success mb-1">
                                            <i class="fa fa-download"></i> Descargar
                                        </a>
                                        @if(\Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion)->isFuture())
                                            <form action="{{ route('estudiantes.tareas.eliminarArchivo', $tareaEstudiante->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar el archivo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge badge-secondary">Sin acción</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Scripts --}}

    <!-- Librerías DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#TareasTable').DataTable({
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
                    "sSearch":         "Buscar:",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sPrevious": "Anterior",
                        "sNext":     "Siguiente",
                        "sLast":     "Último"
                    }
                }
            });


        });
    </script>
@endsection
