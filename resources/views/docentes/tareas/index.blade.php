@extends('crudbooster::admin_template')

@section('content')

@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Gestión de Tareas</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('docentes.tareas.create', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Crear Nueva Tarea
            </a>
        </div>
    </div>

    <div class="box-body">

        <!-- Buscador -->
        <form method="GET" action="{{ url()->current() }}" class="mb-3">
            <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar tarea o materia..."
                       value="{{ $buscar ?? '' }}" style="width: 300px;">
        
                <select name="bimestre" class="form-control" style="width: 200px;">
                    <option value="">-- Seleccionar Bimestre --</option>
                    @foreach($bimestres as $b)
                        <option value="{{ $b->nombre }}" {{ ($bimestre ?? '') === $b->nombre ? 'selected' : '' }}>
                            {{ $b->nombre }}
                        </option>
                    @endforeach
                </select>
        
                <button type="submit" class="btn btn-primary" name="buscar_btn" value="1">Buscar</button>
        
                @if($buscar || $bimestre)
                    <button type="submit" class="btn btn-warning" name="limpiar" value="1">Limpiar Filtros</button>
                @endif
            </div>
        </form>
        
      

        <div class="table-responsive">
            <table class="table table-bordered" id="tabla-tareas">
                <thead>
                    <tr>
                        <th>Bimestre</th>
                        <th>Título</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Grado</th>
                        <th>Curso</th>
                        <th>Sección</th>
                        <th>Jornada</th>
                        <th>Fecha de Expiración</th>
                        <th>Punteo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tareas as $tarea)
                        @php $query = request()->query(); @endphp
                        <tr>
                            <td>{{ $tarea->bimestre->nombre ?? 'No definido' }}</td>
                            <td>{{ $tarea->titulo }}</td>
                            <td>{{ $tarea->materia->gestionMateria->nombre ?? 'No definido' }}</td>
                            <td>{{ $tarea->docente->persona->nombres ?? 'No definido' }}</td>
                            <td>{{ $tarea->materia->cgshe->grados->nombre ?? 'No definido' }}</td>
                            <td>{{ $tarea->materia->cgshe->cursos->curso ?? 'No definido' }}</td>
                            <td>{{ $tarea->materia->cgshe->secciones->seccion ?? 'No definido' }}</td>
                            <td>{{ $tarea->materia->cgshe->jornadas->jornada->nombre ?? 'No definido' }}</td>
                            <td>{{ $tarea->fexpiracion ?? 'Sin fecha' }}</td>
                            <td>{{ $tarea->punteo ?? 'Sin Punteo' }}</td>
                            <td>{{ $tarea->estado->estado ?? 'No definido' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('docentes.tareas.asignar.form', [$tarea->id] + $query) }}" class="btn btn-primary btn-sm" title="Asignar estudiantes">
                                        <i class="fa fa-user-plus"></i>
                                    </a>
                                    <a href="{{ route('docentes.tareas.show', [$tarea->id] + $query) }}" class="btn btn-info btn-sm" title="Ver tarea">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('docentes.tareas.edit', [$tarea->id] + $query) }}" class="btn btn-warning btn-sm" title="Editar tarea">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('docentes.tareas.destroy', [$tarea->id] + $query) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta tarea?')" title="Eliminar tarea">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div> <!-- end box-body -->
</div> <!-- end box -->

<!-- Librerías DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Scripts de Filtro y DataTable -->
<script>
$(document).ready(function() {
    var table = $('#tabla-tareas').DataTable({
        paging: false,
        language: {
                decimal: "",
                emptyTable: "No hay datos disponibles en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron resultados",
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
            },
            paging: true,
            info: true,
            ordering: true
        });

   
});
</script>

@endsection
