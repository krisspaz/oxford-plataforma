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
        <h1 class="box-title">Calificar Tareas  </h1>

    </div>
    <div class="box-body">




        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

      <!-- Buscador -->
      <form method="GET" action="{{ url()->current() }}" class="mb-3">
        <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar tarea o materia..."
                   value="{{ $buscar ?? '' }}" style="width: 300px;">

            <select name="bimestre" class="form-control" style="width: 200px;">
                <option value="">-- Seleccionar Bimestre --</option>
                @foreach($bimestres as $b)
                    <option value="{{ $b->nombre }}" {{ ($bimestreFiltro ?? '') === $b->nombre ? 'selected' : '' }}>
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


        <br>



        <table class="table table-bordered">
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
                    <th>Pendientes de Calificar</th>

                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                @foreach($tareas as $tarea)

                    <tr>
                        <td>{{ $tarea->bimestre->nombre }}</td>
                        <td>{{ $tarea->titulo }}</td>
                        <td>{{ $tarea->materia->gestionMateria->nombre }}</td>
                        <td>{{ $tarea->docente->persona->nombres }}{{ $tarea->docente->persona->apellidos }}</td>
                        <td>{{ $tarea->materia->cgshe->grados->nombre }}</td>
                        <td>{{ $tarea->materia->cgshe->cursos->curso }}</td>
                        <td>{{ $tarea->materia->cgshe->secciones->seccion }}</td>
                        <td>{{ $tarea->materia->cgshe->jornadas->jornada->nombre }}</td>
                        <td>{{ $tarea->fexpiracion }}</td>
                        <td>{{ $tarea->pendientes_count }}</td> <!-- Aquí se muestra el número de pendientes -->

                        <td>
                            <a href="{{ route('calificaciones.calificar', $tarea->id) }}" class="btn btn-primary">
                                Calificar
                            </a>
                            <a href="{{ route('calificaciones.calificadas', $tarea->id) }}" class="btn btn-success">
                                Ver Calificadas
                            </a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('.table').DataTable({
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
