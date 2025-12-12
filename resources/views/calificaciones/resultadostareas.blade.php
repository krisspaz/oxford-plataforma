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

    <div class="box-header with-border">
        <h3 class="box-title">Tareas - Ciclo Escolar {{ $anio_ciclo_escolar }}</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <h4><strong>Estudiante:</strong> {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</h4>
                <h4><strong>Grado:</strong> {{ $estudiante->asignacion->grados->nombre }}</h4>
            </div>
            <div class="col-md-6">
                <h4><strong>Curso:</strong> {{ $estudiante->asignacion->cursos->curso }}</h4>
                <h4><strong>Jornada:</strong> {{ $estudiante->asignacion->jornadas->jornada->nombre }}</h4>
            </div>
        </div>

        <!-- Filtros y buscador -->
        <div class="row mt-3 mb-3">
            <div class="col-md-3">
                <select id="filterBimestre" class="form-control">
                    <option value="">-- Filtrar por Bimestre --</option>
                    @foreach($bimestres as $bimestre)
                        <option value="{{ $bimestre->nombre }}"
                            {{ $bimestre->id == $ultimoBimestre ? 'selected' : '' }}>
                            {{ $bimestre->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select id="filterMateria" class="form-control">
                    <option value="">-- Filtrar por Materia --</option>
                  @foreach($materias as $materia)
                   <option value="{{ $materia->gestionMateria->nombre }}">
                        {{ $materia->gestionMateria->nombre }}
                    </option>
                @endforeach
                </select>
            </div>



            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar tarea...">
            </div>
        </div>

        <table class="table table-striped table-bordered" id="tareasTable">
            <thead>
                <tr>
                    <th>Estado de la Tarea</th>
                    <th>Bimestre</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Valor de la Actividad</th>
                    <th>Fecha de Entrega</th>

                    <th>Materia</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareasCalificadas as $tareaEstudiante)
                    <tr>
                        <td>
                            @php
                                $fechaEntrega = \Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion);
                                $hoy = \Carbon\Carbon::now();
                                $estado = $tareaEstudiante->estado;

                                if ($hoy->gt($fechaEntrega) && $estado !== 'entregada') {



                                        $displayEstado = $estado;
                                    $color = 'green';



                                } elseif ($estado === 'entregada') {
                                    $displayEstado = 'Entregada';
                                    $color = 'green';
                                } elseif ($estado === 'pendiente') {
                                    $displayEstado = 'Pendiente';
                                    $color = 'orange';

                                } else {
                                    $displayEstado = $estado;
                                    $color = 'black';
                                }
                            @endphp

                            <span style="color: {{ $color }}; font-weight: bold;">
                                {{ $displayEstado }}
                            </span>
                        </td>

                        <td>{{ $tareaEstudiante->tarea->bimestre->nombre }}</td>
                        <td>{{ $tareaEstudiante->tarea->titulo }}</td>
                        <td>{{ $tareaEstudiante->tarea->descripcion }}</td>
                        <td>{{ $tareaEstudiante->tarea->punteo }}</td>
                        <td>{{ \Carbon\Carbon::parse($tareaEstudiante->tarea->fexpiracion)->format('d/m/Y') }}</td>

                        <td>{{ $tareaEstudiante->tarea->materia->gestionMateria->nombre }}</td>
                        <td>
                            {{ $tareaEstudiante->tarea->docente->persona->nombres }}
                            {{ $tareaEstudiante->tarea->docente->persona->apellidos }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay tareas calificadas para este estudiante.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-center">

        </div>
    </div>
</div>

@push('bottom')
<script>
    const searchInput = document.getElementById('searchInput');
    const filterBimestre = document.getElementById('filterBimestre');
    const filterMateria = document.getElementById('filterMateria');

    function filtrarTareas() {
        const searchValue = searchInput.value.toLowerCase();
        const bimestreValue = filterBimestre.value.toLowerCase();
        const materiaValue = filterMateria.value.toLowerCase();

        const rows = document.querySelectorAll("#tareasTable tbody tr");

        rows.forEach(function(row) {
            const rowText = row.textContent.toLowerCase();
            const rowBimestre = row.cells[1].textContent.toLowerCase();
            const rowMateria = row.cells[6].textContent.toLowerCase();

            const mostrar = rowText.includes(searchValue) &&
                            (bimestreValue === "" || rowBimestre === bimestreValue) &&
                            (materiaValue === "" || rowMateria === materiaValue);

            row.style.display = mostrar ? "" : "none";
        });
    }

    searchInput.addEventListener('keyup', filtrarTareas);
    filterBimestre.addEventListener('change', filtrarTareas);
    filterMateria.addEventListener('change', filtrarTareas);
</script>
@endpush
@endsection

