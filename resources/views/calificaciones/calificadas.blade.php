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
        <h1 class="box-title">Tareas Calificadas</h1>
        <p><strong>Tarea:</strong> {{ $tarea->titulo }}</p>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Carné</th>
                    <th>Estudiante</th>
                    <th>Valor de la Actividad</th>
                    <th>Calificación</th>
                    <th>Comentarios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tareasCalificadas as $tareaEstudiante)
                    <tr>
                        <td>{{ $tareaEstudiante->estudiante->carnet }} </td>
                        <td>{{ $tareaEstudiante->estudiante->persona->apellidos }} {{ $tareaEstudiante->estudiante->persona->nombres }}</td>
                        <td>
                            {{ $tareaEstudiante->tarea->punteo ?? 'Sin Asignar' }}
                        </td>
                        <td>{{ $tareaEstudiante->calificacion->calificacion }}</td>
                        <td>{{ $tareaEstudiante->calificacion->comentarios }}</td>
                        <td>
                            <a href="{{ route('calificaciones.edit', $tareaEstudiante->calificacion->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('calificaciones.destroy', $tareaEstudiante->calificacion->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta calificación?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
