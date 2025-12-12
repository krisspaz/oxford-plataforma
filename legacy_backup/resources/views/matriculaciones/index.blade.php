@extends('layouts.app')

@section('content')

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
<div class="container">
    <h1>Matriculaciones</h1>
    <a href="{{ route('matriculaciones.create') }}" class="btn btn-primary mb-3">Nueva Matriculación</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Paquete</th>
                <th>Fecha de Inscripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriculaciones as $matriculacion)
            <tr>
                <td>{{ $matriculacion->id }}</td>
                <td>{{ $matriculacion->estudiante->nombre }}</td>
                <td>{{ $matriculacion->paquete->nombre }}</td>
                <td>{{ $matriculacion->fecha_inscripcion }}</td>
                <td>{{ $matriculacion->estado->nombre }}</td>
                <td>
                    <a href="{{ route('matriculaciones.show', $matriculacion->id) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('matriculaciones.edit', $matriculacion->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('matriculaciones.destroy', $matriculacion->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
