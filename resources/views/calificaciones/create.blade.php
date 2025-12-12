@extends('crudbooster::admin_template')
@section('content')
div class="box">
    <div class="box-header">
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
        <h1 class="box-title">Mis Tareas</h1>
        <!-- Botón para agregar calificación general (opcional) -->
        <a href="{{ route('calificaciones.create', ['tareaEstudianteId' => 0]) }}" class="btn btn-primary pull-right" style="margin-top: -5px;">
            <i class="fa fa-plus"></i> Crear Calificación General
        </a>
    </div>

    <div class="box-body">

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    
    <form action="{{ route('calificaciones.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="materia_id">Materia</label>
            <select class="form-control" id="materia_id" name="materia_id" required>
                <option value="">Seleccione una materia</option>
                @foreach($materias as $materia)
                    <option value="{{ $materia->id }}">{{ $materia->gestionMateria->nombre }}: {{ $materia->cgshe->grados->nombre }} {{ $materia->cgshe->cursos->curso }}, Seccion: "{{ $materia->cgshe->secciones->seccion }}", Jornada: "{{ $materia->cgshe->jornadas->jornada->nombre }}"</option>
                @endforeach
            </select>
        </div>

    <div>
        
        <table id="TareasEstudiantesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Estudiante</th>
                    <th>Tarea</th>
                    <th>Descripción</th>
                    <th>Fecha de Entrega</th>
                    <th>Estado</th>
                    <th>Acciones</th>  <!-- Columna de acciones -->
                    <th>Nota</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tareasEstudiantes as $tareaEstudiante)
                    <tr>
                        <td>{{ $tareaEstudiante->tarea->materia->nombre }}</td>
                        <td>{{ $tareaEstudiante->tarea->docente->persona->nombres }} {{ $tareaEstudiante->tarea->docente->persona->apellidos }}</td>
                        <td>{{ $tareaEstudiante->estudiante->persona->nombres }} {{ $tareaEstudiante->estudiante->persona->apellidos }}</td>
                        <td>{{ $tareaEstudiante->tarea->titulo }}</td>
                        <td>{{ $tareaEstudiante->tarea->descripcion }}</td>
                        <td>{{ \Carbon\Carbon::parse($tareaEstudiante->fecha_entrega)->format('d/m/Y') }}</td>
                        <td>{{ $tareaEstudiante->estado }}</td>
                        <td>
                            @if($tareaEstudiante->estado == 'entregada')
                                <!-- Descargar tarea -->
                                <a href="{{ route('estudiantes.tareas.descargar', $tareaEstudiante->id) }}" class="btn btn-success" data-toggle="tooltip" title="Descargar Tarea">
                                    <i class="fa fa-download"></i> Descargar
                                </a>
                            @else
                                <span class="badge badge-secondary">No Presentó Tarea</span>
                            @endif

                            <!-- Botón para crear calificación -->
                            <a href="{{ route('calificaciones.create', ['tareaEstudianteId' => $tareaEstudiante->id]) }}" class="btn btn-primary" title="Crear Calificación">
                                <i class="fa fa-plus"></i> Calificar
                            </a>
                        </td>
                        <td>
                            <input type="number" id="nota" name="nota" class="form-control" style="width: 100px" required>
                        </td>
                        <td>
                            <input type="text" id="observacion" name="observacion" class="form-control" style="width: 100px" required>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="calificacion">Calificación</label>
                                <input type="number" name="calificacion" id="calificacion" class="form-control" min="0" max="100" step="0.00" required>
                            </div>
                    
                            <div class="form-group">
                                <label for="comentarios">Comentarios</label>
                                <textarea name="comentarios" id="comentarios" class="form-control" rows="3"></textarea>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $tareasEstudiantes->links('pagination::bootstrap-4') }}
        </div>

       
    </div>
</div>
</div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('calificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection