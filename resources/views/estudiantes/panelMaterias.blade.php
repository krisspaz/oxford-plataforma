@extends('crudbooster::admin_template')

@section('content')
<div class="container py-5">

    {{-- Mensajes Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Título --}}
    <div class="text-center mb-5">
        <h2 class="text-primary font-weight-bold">
            <i class="fa fa-book-open mr-2"></i> Panel de Materias
        </h2>
    </div>

    @if ($materiasPorCurso->isNotEmpty())
        <div class="row justify-content-center">
            @foreach ($materiasPorCurso as $materia)
                <div class="col-sm-10 col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-lg border-0 h-100 rounded-lg bg-light">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="text-dark font-weight-bold">
                                    {{ $materia->gestionMateria->nombre }}
                                </h5>
                                <p class="text-muted mb-2">
                                    <i class="fa fa-book mr-1"></i> {{ $estudiantes->id }}
                                </p>

                            </div>

                            <form action="{{ route('estudiante.contenido', ['materiaId' => $materia->id, 'Inscripcion2' => $inscripcion2]) }}" method="GET">
                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    <i class="fa fa-folder-open mr-1"></i> Ver Contenidos
                                </button>

                            </form>

                            <form action="{{ route('estudiantes.tareas.porMateria', ['materiaId' => $materia->id]) }}" method="GET">
                                <button type="submit" class="btn btn-warning btn-block mt-2">
                                    <i class="fa fa-tasks mr-1"></i> Ver Tareas
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center mt-4">
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-circle mr-1"></i> No hay materias asignadas actualmente.
            </div>
        </div>
    @endif
</div>
@endsection
