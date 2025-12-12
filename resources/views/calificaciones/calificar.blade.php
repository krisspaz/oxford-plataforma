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
        <h1 class="box-title">Tareas</h1>
    </div>
    <div class="box-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Formulario para Guardar Todo -->
        <form action="{{ route('calificaciones.storeMultiple') }}" method="POST">
            @csrf

         
            <input type="hidden" name="materia_id" value="{{ $tarea->materia_id }}">
            <input type="hidden" name="bimestre" value="{{ $tarea->bimestre->id }}">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Carné</th>
                        <th>Estudiante</th>
                        <th>Tarea Subida</th>
                        <th>Valor de la Actividad</th>
                        <th>Calificación</th>
                        <th>Comentario</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($tareasEstudiantes as $index => $tareaEstudiante)
                        <tr>
                            <td>{{ $tareaEstudiante->estudiante->carnet }}</td>
                            <td>{{ $tareaEstudiante->estudiante->persona->apellidos }} {{ $tareaEstudiante->estudiante->persona->nombres}}</td>
        
                            <td>
                                @if($tareaEstudiante->estado == 'entregada')
                                    <a href="{{ route('estudiantes.tareas.descargar', $tareaEstudiante->id) }}" class="btn btn-success">
                                        <i class="fa fa-download"></i> Descargar
                                    </a>
                                @else
                                    <span class="badge badge-secondary">No Presentó Tarea</span>
                                @endif
                            </td>
                            <td>
                                {{ $tareaEstudiante->tarea->punteo ?? 'Sin Asignar' }}
                            </td>
        
                            <td>
                                <input type="hidden" name="estudiante_id[]" value="{{ $tareaEstudiante->estudiante->id}}">
                                <input type="hidden" name="tarea_estudiante_id[]" value="{{ $tareaEstudiante->id }}">
                                <input type="number" name="calificaciones[]" class="form-control" min="0" max="{{ $tareaEstudiante->tarea->punteo ?? 100 }}"  step="0.01" required>

                            </td>
                            <td>
                                <input type="text" name="comentarios[]" class="form-control" maxlength="500">
                            </td>
                            

                            
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Todo
            </button>
        </form>
        

    </div>
</div>

@endsection