@extends('crudbooster::admin_template')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Tareas Calificadas</h3>
    </div>

    <form method="GET" action="{{ route('cuadro_notas.reportes') }}">
        @csrf
        <div class="box-body">

            <input type="hidden" name="estudiante_id" value="{{ $estudiantes->id }}">

            <div class="form-group">
                <label for="materia">Materia</label>
                <select name="materia_id" id="materia_id" class="form-control">
                    <option value="">Todas las Materias</option>
                    @foreach ($materiasPorCurso as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->gestionMateria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="bimestre_id">Bimestre</label>
                <select name="bimestre_id" id="bimestre_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($bimestres->unique('nombre') as $bimestre)
                        <option value="{{ $bimestre->id }}" {{ request('bimestre_id') == $bimestre->id ? 'selected' : '' }}>
                            {{ $bimestre->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="ciclo_escolar_id">Ciclo Escolar</label>
                <select name="ciclo_escolar_id" id="ciclo_escolar_id" class="form-control" required>
                    <option value="">Seleccione un Ciclo Escolar</option>
                    @foreach ($ciclosEscolares as $ciclo)
                        <option value="{{ $ciclo->ciclo_escolar }}" {{ request('ciclo_escolar_id') == $ciclo->ciclo_escolar ? 'selected' : '' }}>
                            {{ $ciclo->ciclo_escolar }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-print"></i> Filtrar
            </button>
        </div>
    </form>

    {{-- Resultados --}}
    @if(isset($calificaciones) && count($calificaciones))
        @php
            $agrupadas = $calificaciones->groupBy(function($item) {
                return $item->tareaEstudiante->estudiante->id . '-' .
                       $item->tareaEstudiante->estudiante->asignacion->grados->nombre . '-' .
                       $item->tareaEstudiante->tarea->bimestre->nombre;
            });
        @endphp

        @foreach($agrupadas as $grupo => $items)
            @php
                $estudiante = $items->first()->tareaEstudiante->estudiante;
                $curso = $estudiante->asignacion->grados->nombre;
                $bimestre = $items->first()->tareaEstudiante->tarea->bimestre->nombre;
                $porMateria = $items->groupBy('tareaEstudiante.tarea.materia.gestionMateria.nombre');
            @endphp

            <div class="box box-info estudiante-box">
                <div class="box-header">
                    <strong>Estudiante:</strong> {{ $estudiante->persona->apellidos }} {{ $estudiante->persona->nombres }} <br>
                    <strong>Curso:</strong> {{ $curso }} <br>
                    <strong>Bimestre:</strong> {{ $bimestre }}
                </div>
                <div class="box-body">
                    @foreach($porMateria as $materia => $calificacionesMateria)
                        <h4><strong>Materia:</strong> {{ $materia ?? 'Sin nombre' }}</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Estado</th>
                                    <th>Calificación</th>
                                    <th>Comentario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calificacionesMateria as $calificacion)
                                    <tr>
                                        <td>{{ $calificacion->tareaEstudiante->tarea->titulo }}</td>
                                        <td>{{ $calificacion->tareaEstudiante->tarea->descripcion }}</td>
                                        <td>{{ \Carbon\Carbon::parse($calificacion->tareaEstudiante->tarea->fexpiracion)->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($calificacion->tareaEstudiante && $calificacion->tareaEstudiante->fecha_entrega)
                                                Tarea Entregada
                                            @else
                                                Sin Entregar
                                            @endif
                                        </td>
                                        <td>{{ $calificacion->calificacion }}</td>
                                        <td>{{ $calificacion->comentarios }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        @if(request()->all())
            <div class="alert alert-info">
                No hay calificaciones disponibles para los filtros seleccionados.
            </div>
        @endif
    @endif

</div>
@endsection
