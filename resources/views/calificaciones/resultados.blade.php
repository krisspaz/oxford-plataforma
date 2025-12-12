@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    {{-- ALERTAS --}}
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

    {{-- SWEET ALERT --}}
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
        <h3 class="box-title">Tareas Calificadas - Ciclo Escolar {{ $anio_ciclo_escolar }}</h3>
    </div>

    <div class="box-body">
        {{-- FILTROS --}}
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-4">
                <label for="filtroMateria">Filtrar por Materia:</label>
                <select id="filtroMateria" class="form-control">
                    <option value="">Todas las materias</option>
                    @php
                        $materiasUnicas = $tareasCalificadas->pluck('tarea.materia.gestionMateria')->unique('id');
                    @endphp
                    @foreach($materiasUnicas as $materia)
                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filtroDocente">Filtrar por Docente:</label>
                <select id="filtroDocente" class="form-control">
                    <option value="">Todos los docentes</option>
                    @php
                        $docentesUnicos = $tareasCalificadas->map(function($item){
                            return [
                                'id' => $item->tarea->docente->id,
                                'nombre' => $item->tarea->docente->persona->nombres.' '.$item->tarea->docente->persona->apellidos
                            ];
                        })->unique('id');
                    @endphp
                    @foreach($docentesUnicos as $docente)
                        <option value="{{ $docente['id'] }}">{{ $docente['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" style="padding-top: 25px;">
                <button id="btnPDF" class="btn btn-primary">Descargar PDF</button>
            </div>
        </div>

        {{-- INFO ESTUDIANTE --}}
        <div class="row" style="margin-bottom:20px;">
            <div class="col-md-6">
                <h4><strong>Bimestre:</strong> {{ $tareasCalificadas->first()->tarea->bimestre->nombre }}</h4>
                <h4><strong>Estudiante:</strong> {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</h4>
                <h4><strong>Grado:</strong> {{ $estudiante->asignacion->grados->nombre }}</h4>
            </div>
            <div class="col-md-6">
                <h4><strong>Curso:</strong> {{ $estudiante->asignacion->cursos->curso }}</h4>
                <h4><strong>Jornada:</strong> {{ $estudiante->asignacion->jornadas->jornada->nombre }}</h4>
            </div>
        </div>

        @php
            $agrupadas = $tareasCalificadas->groupBy(function($item) {
                return $item->tarea->materia->gestionMateria->nombre . ' - Docente: ' .
                    $item->tarea->docente->persona->nombres . ' ' . $item->tarea->docente->persona->apellidos;
            });
        @endphp

        {{-- TABLA --}}
        <table class="table table-striped table-bordered" id="tablaTareas">
            <tbody>
                @forelse($agrupadas as $grupo => $tareas)
                    <tr class="grupo-header"
                        data-materia-id="{{ $tareas->first()->tarea->materia->gestionMateria->id }}"
                        data-docente-id="{{ $tareas->first()->tarea->docente->id }}">
                        <td colspan="6" style="background-color: #f0f0f0; font-size: 16px;">
                            <strong>{{ $grupo }}</strong>
                        </td>
                    </tr>
                    <tr style="background-color: #e0e0e0; font-weight: bold;">
                        <td>Tarea</td>
                        <td>Estado de la Entrega</td>
                        <td>Fecha Entregada</td>
                        <td>Calificación</td>
                        <td>Comentario</td>
                        <td>Fecha de Calificación</td>
                    </tr>
                    @foreach($tareas as $tareaEstudiante)
                        <tr>
                            <td>{{ $tareaEstudiante->tarea->titulo }}</td>
                            <td>{{ $tareaEstudiante->estado }}</td>
                            <td>{{ $tareaEstudiante->estado === 'pendiente' ? '' : \Carbon\Carbon::parse($tareaEstudiante->fecha_entrega)->format('d/m/Y') }}</td>
                            <td style="color: {{ $tareaEstudiante->calificacion->calificacion === null ? 'gray' : ($tareaEstudiante->calificacion->calificacion <= 59 ? 'red' : 'green') }}; font-weight: bold;">
                                {{ $tareaEstudiante->calificacion->calificacion ?? 'Sin Calificar' }}
                            </td>
                            <td>{{ $tareaEstudiante->calificacion->comentarios }}</td>
                            <td>
                                @if($tareaEstudiante->calificacion->calificacion === null)
                                    Sin Calificar
                                @else
                                    {{ $tareaEstudiante->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay tareas calificadas para este estudiante.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT DE FILTRO --}}
<script>
document.getElementById('filtroMateria').addEventListener('change', filtrar);
document.getElementById('filtroDocente').addEventListener('change', filtrar);

function filtrar() {
    var materia = document.getElementById('filtroMateria').value;
    var docente = document.getElementById('filtroDocente').value;

    document.querySelectorAll('#tablaTareas .grupo-header').forEach(function(header) {
        var grupoMateria = header.getAttribute('data-materia-id');
        var grupoDocente = header.getAttribute('data-docente-id');
        var visible = true;

        if (materia && grupoMateria !== materia) visible = false;
        if (docente && grupoDocente !== docente) visible = false;

        var nextRow = header.nextElementSibling;
        while (nextRow && !nextRow.classList.contains('grupo-header')) {
            nextRow.style.display = visible ? '' : 'none';
            nextRow = nextRow.nextElementSibling;
        }

        header.style.display = visible ? '' : 'none';
    });
}

// BOTÓN DESCARGAR PDF CON FILTROS
document.getElementById('btnPDF').addEventListener('click', function() {
    var materia = document.getElementById('filtroMateria').value;
    var docente = document.getElementById('filtroDocente').value;
    var bimestre = {{ $tareasCalificadas->first()->tarea->bimestre->id }};
    var anio = "{{ urlencode($anio_ciclo_escolar) }}";
    var estudiante = {{ $estudiante->id }};

    var url = "{{ route('tareas.pdf', ['estudiante' => ':estudiante']) }}".replace(':estudiante', estudiante);
    url += '?bimestre_id=' + bimestre;
    url += '&anio_ciclo_escolar=' + anio;
    if(materia) url += '&materia_id=' + materia;
    if(docente) url += '&docente_id=' + docente;

    window.open(url, '_blank');
});
</script>
@endsection
