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

    {{-- SweetAlert para feedback visual --}}
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
        <h3 class="box-title">Listado de Notas Finales</h3>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-striped">
            <tbody>
                @php
                    $notasOrdenadas = $notas->sortBy(function($item) {
                        return $item->estudiante->persona->apellidos . ' ' . $item->estudiante->persona->nombres;
                    });
                    $agrupadas = $notasOrdenadas->groupBy('estudiante_id');
                @endphp

                @foreach($agrupadas as $estudianteId => $notasEstudiante)
                    @php
                        $estudiante = $notasEstudiante->first()->estudiante;
                        $carnet = $estudiante->carnet;
                        $nombreCompleto = $estudiante->persona->apellidos . ' ' . $estudiante->persona->nombres;
                        $grado = $estudiante->cgshges->grados->nombre ?? '-';
                        $curso = $estudiante->cgshges->cursos->curso ?? '-';
                    @endphp

                    {{-- Título del grupo por estudiante --}}
                    <tr style="background-color: #e9ecef;">
                        <td colspan="7">
                            <a href="{{ route('boleta.estudiante', $estudiante->id) }}" class="btn btn-sm btn-info">Ver Boleta</a>
                            <strong>Nombre Completo:</strong> {{ $nombreCompleto }} &nbsp;&nbsp;&nbsp;
                            <strong>Carné:</strong> {{ $carnet }}
                        </td>
                    </tr>

                    {{-- Encabezados por cada estudiante --}}
                    <tr style="background-color: #f8f9fa;">

                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Curso</th>
                        <th>Bimestre</th>
                        <th>Nota Bimestral</th>
                        <th>Ciclo Escolar</th>
                    </tr>

                    {{-- Notas individuales --}}
                    @foreach($notasEstudiante as $nota)
                        <tr>

                            <td>{{ $nota->materia->gestionMateria->nombre }}</td>
                            <td>{{ $grado }}</td>
                            <td>{{ $curso }}</td>
                            <td>{{ $nota->bimestres->nombre }}</td>
                            <td>{{ $nota->nota_acumulada }}</td>
                            <td>{{ $nota->ciclo_escolar }}</td>
                          
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection
