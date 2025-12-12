@extends('crudbooster::admin_template')

@section('content')
<div class="box">

    {{-- Mensajes de sesión --}}
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

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}'
            });
        </script>
    @endif

    <div class="box-header">
        <h3 class="box-title">Listado de Notas Finales</h3>
    </div>

    @php
        $gradosUnicos = $notas->map(fn($n) => $n->estudiante->cgshges->grados->nombre ?? null)
                              ->filter()->unique()->sort();

        $materiasUnicas = $notas->map(fn($n) => $n->materia->gestionMateria->nombre ?? null)
                                ->filter()->unique()->sort();
    @endphp

    <form method="GET" action="{{ url()->current() }}" class="form-inline" style="margin-bottom:20px;">
        <div class="form-group" style="margin-right:15px;">
            <label>Grado:</label>
            <select name="grado_nombre" class="form-control" onchange="this.form.submit()">
                <option value="">-- Todos --</option>
                @foreach($gradosUnicos as $grado)
                    <option value="{{ $grado }}" {{ request('grado_nombre') == $grado ? 'selected' : '' }}>
                        {{ $grado }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-right:15px;">
            <label>Materia:</label>
            <select name="materia_nombre" class="form-control" onchange="this.form.submit()">
                <option value="">-- Todas --</option>
                @foreach($materiasUnicas as $materia)
                    <option value="{{ $materia }}" {{ request('materia_nombre') == $materia ? 'selected' : '' }}>
                        {{ $materia }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Selector cantidad por página --}}
        <div class="form-group" style="margin-right:15px;">
            <label>Mostrar:</label>
            <select name="per_page" class="form-control" onchange="this.form.submit()">
                @foreach([5,10,25,50,100] as $size)
                    <option value="{{ $size }}" {{ request('per_page', $perPage) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Todos</option>
            </select>
        </div>

        @if(request('grado_nombre') || request('materia_nombre'))
            <a href="{{ url()->current() }}" class="btn btn-default">Limpiar</a>
        @endif
    </form>

    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-striped">
            <tbody>
                @php
                    $notasFiltradas = $notas->filter(function ($nota) {
                        $gradoFiltro = request('grado_nombre');
                        $materiaFiltro = request('materia_nombre');

                        $gradoActual = $nota->estudiante->cgshges->grados->nombre ?? null;
                        $materiaActual = $nota->materia->gestionMateria->nombre ?? null;

                        return (!$gradoFiltro || $gradoFiltro == $gradoActual)
                            && (!$materiaFiltro || $materiaFiltro == $materiaActual);
                    });

                    $notasOrdenadas = $notasFiltradas->sortBy(fn($i) =>
                        $i->estudiante->persona->apellidos.' '.$i->estudiante->persona->nombres
                    );

                    $agrupadas = $notasOrdenadas->groupBy('estudiante_id');
                @endphp

                @foreach($agrupadas as $notasEstudiante)
                    @php
                        $est = $notasEstudiante->first()->estudiante;
                    @endphp

                    <tr style="background:#e9ecef;">
                        <td colspan="8">
                            <strong>Nombre:</strong> {{ $est->persona->apellidos.' '.$est->persona->nombres }}
                            &nbsp;&nbsp;
                            <strong>Carné:</strong> {{ $est->carnet }}
                        </td>
                    </tr>

                    <tr style="background:#f8f9fa;">
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Curso</th>
                        <th>Bimestre</th>
                        <th>Nota</th>
                        <th>Porcentaje</th>
                        <th>Ciclo</th>
                        <th>Acciones</th>
                    </tr>

                    @foreach($notasEstudiante as $nota)
                    <tr>
                        <td>{{ $nota->materia->gestionMateria->nombre }}</td>
                        <td>{{ $est->cgshges->grados->nombre }}</td>
                        <td>{{ $est->cgshges->cursos->curso }}</td>
                        <td>{{ $nota->bimestres->nombre }}</td>
                        <td>{{ $nota->nota_acumulada }}</td>
                        <td>{{ ($nota->nota_acumulada * $nota->bimestres->porcentaje) / $nota->bimestres->punteo_maximo }}%</td>
                        <td>{{ $nota->ciclo_escolar }}</td>
                        <td>
                            <a href="{{ route('cuadro-notas.show', $nota->id) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>

                            @if($nota->cierre === 'No')
                                <a href="{{ route('cuadro-notas.edit', $nota->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>

                                <form action="{{ route('cuadro-notas.destroy', $nota->id) }}"
                                      method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs"
                                        onclick="return confirm('¿Eliminar nota?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                @endforeach
            </tbody>
        </table>

        {{-- PAGINACIÓN CORREGIDA --}}
        <div class="text-center">

            @php
                $mostrarTodo = request('per_page') === 'all';
            @endphp

            @if(!$mostrarTodo && $notas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $notas->links() }}
            @endif

        </div>

    </div>
</div>
@endsection
