<!-- resources/views/ajustes_asignacion/index.blade.php -->
@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">

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
    <div class="box-header with-border">
        <i class="fa fa-users"></i>
        <h3 class="box-title">Lista de Estudiantes</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('ajustes_asignacion.create') }}" class="btn btn-sm btn-success">
                <i class="fa fa-plus"></i> Agregar Estudiante
            </a>
        </div>
    </div>

    <div class="box-body">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa fa-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Inscripción</th>
                        <th>Nivel</th>
                        <th>Estado</th>
                        <th class="text-center" width="150px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estudiantes as $estudiante)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}" alt="Foto Estudiante" width="50" class="img-thumbnail">
                            </td>
                            <td>{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</td>
                            <td>

                                @if($estudiante->inscripciones->isNotEmpty())
                                {{ $estudiante->inscripciones->last()->id }}
                            @else
                                <span class="text-muted">No inscrito</span>
                            @endif


                            </td>
                            <td>{{ $estudiante->nivel->nombre }}</td>
                            <td>{{ $estudiante->estado->nombre }}</td>
                            <td class="text-center">
                                <a href="{{ route('ajustes_asignacion.edit', $estudiante->id) }}" class="btn btn-xs btn-warning" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('ajustes_asignacion.destroy', $estudiante->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($estudiantes->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay estudiantes registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
