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
    <h2 class="text-center">Lista de Materias Asignadas</h2>
    <a href="{{ route('materiascursos.create') }}" class="btn btn-primary mb-3">Nueva Asignación</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Docente</th>
                <th>Materias Asignadas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @php
                $docentesAgrupados = $materiasCursos->groupBy('docente_id');
            @endphp
            @foreach($docentesAgrupados as $docenteId => $materias)
                @php
                    $docente = $materias->first()->docente;
                @endphp
                <tr>
                    <td>{{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}</td>
                    <td>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Curso</th>
                                    <th>Grado</th>
                                    <th>Sección</th>
                                    <th>Jornada</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materias as $mc)
                                    <tr>
                                        <td>{{ $mc->materia->gestionMateria->nombre }}</td>
                                        <td>{{ $mc->cgshges->cursos->curso }}</td>
                                        <td>{{ $mc->cgshges->grados->nombre }}</td>
                                        <td>{{ $mc->cgshges->secciones->seccion }}</td>
                                        <td>{{ $mc->cgshges->jornadas->jornada->nombre }}</td>
                                        <td>{{ $mc->estado->estado }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <a href="{{ route('materiascursos.show', $docenteId) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('materiascursos.edit', $docenteId) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('materiascursos.destroy', $docenteId) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar asignaciones del docente?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
