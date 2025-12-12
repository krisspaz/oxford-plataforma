@extends('crudbooster::admin_template')

@section('title', 'Inscripcion de ALumnos')


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
    <h1>Buscar Inscripción</h1>
    
    <form action="{{ route('inscripcion.search') }}" method="GET" class="mb-3">
    <input type="hidden" name="view" value="search2">
       
        <div class="form-group">
            <label for="codigo_familiar">Código Familiar</label>
            <input type="text" class="form-control" id="codigo_familiar" name="codigo_familiar" placeholder="Ingrese el Código Familiar">
        </div>
        <div class="form-group">
            <label for="num_documento">Número de Documento</label>
            <input type="text" class="form-control" id="num_documento" name="num_documento" placeholder="Ingrese el Número de Documento">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    
    @if(isset($inscripcion))
    <h2>Resultado de la Búsqueda</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código Familiar</th>
                <th>Padre</th>
                <th>Madre</th>
                <th>Encargado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $inscripcion->codigofamiliar }}</td>
                <td>{{ $inscripcion->padre->nombre }} {{ $inscripcion->padre->apellido }}</td>
                <td>{{ $inscripcion->madre->nombre }} {{ $inscripcion->madre->apellido }}</td>
                <td>{{ $inscripcion->encargado->nombre }} {{ $inscripcion->encargado->apellido }}</td>
                <td>
                    <a href="{{ route('inscripcion.show', $inscripcion->id) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('inscripcion.edit', $inscripcion->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('inscripcion.destroy', $inscripcion->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta inscripción?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
    @endif
    
    @if(isset($message))
    <div class="alert alert-info">{{ $message }}</div>
    @endif
</div>
@endsection
