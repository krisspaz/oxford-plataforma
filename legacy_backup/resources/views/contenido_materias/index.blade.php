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
        <h1 class="box-title">Contenido de Materias</h1>
        <a href="{{ route('contenido_materias.create') }}" class="btn btn-success pull-right">Añadir Contenido</a>
    </div>

    <div class="box-body">
        @if($contenidoMaterias->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Docente</th>
                        <th>Bimestre</th>
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Curso</th>
                        <th>Secciòn</th>
                        <th>Jornada</th>
                       
                        <th>Tipo de Contenido</th>
                        <th>Archivo</th>
                       
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contenidoMaterias as $contenido)
                        <tr>


                            
                           
                            <td>{{ $contenido->id }}</td>
                            <td>{{ $contenido->docente->persona->nombres }} {{ $contenido->docente->persona->apellidos }}</td>
                            <td>{{ $contenido->bimestre->nombre }}</td>
                            <td>{{ $contenido->materiaCursos->materia->gestionMateria->nombre }}</td>
                            <td>{{ $contenido->materiaCursos->cgshges->grados->nombre }}</td>
                            <td>{{ $contenido->materiaCursos->cgshges->cursos->curso }}</td>
                            <td>{{ $contenido->materiaCursos->cgshges->secciones->seccion }}</td>
                            <td>{{ $contenido->materiaCursos->cgshges->jornadas->jornada->nombre }}</td>
                            <td>{{ ucfirst($contenido->tipo_contenido) }}</td>
                            <td>
                                @if($contenido->tipo_contenido === 'video')
                                <a href="{{ asset('storage/' . $contenido->archivo) }}" target="_blank" class="btn btn-primary btn-sm">
                                    Ver Video
                                </a>
                                @elseif(in_array($contenido->tipo_contenido, ['audio', 'documento', 'imagen']))
                                    <a href="{{ Storage::url($contenido->archivo) }}" target="_blank" class="btn btn-success btn-sm">
                                        Ver Archivo
                                    </a>
                                @elseif($contenido->tipo_contenido === 'link')
                                    <a href="{{ url($contenido->archivo) }}" target="_blank" class="btn btn-success btn-sm">
                                        Ver Link
                                    </a>
                                @else
                                    <span class="text-muted">Archivo no disponible</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('contenido_materias.show', $contenido->id) }}" class="btn btn-info btn-sm">Ver</a>
                                <a href="{{ route('contenido_materias.edit', $contenido->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('contenido_materias.destroy', $contenido->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este contenido?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay contenido asignado a las materias.</p>
        @endif
    </div>
</div>
@endsection
