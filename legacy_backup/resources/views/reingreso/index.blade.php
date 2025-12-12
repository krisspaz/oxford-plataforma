@extends('crudbooster::admin_template')

@section('content')
    <div class="box">

        @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
        </div>
    @endif

 @if (session('error') && session('continuar'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>

        {{ session('error') }}

        <br><br>

        <a href="{{ route('reingreso.edit', ['id' => session('estudiante_id'), 'continuar' => 1]) }}"
           class="btn btn-sm btn-primary">
           Continuar Inscripción
        </a>
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

 @if(session('error') && session('continuar') && session('estudiante_id'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Cuotas Pendientes',
            html: '{{ session('error') }}<br><br>¿Desea continuar con la inscripción?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Continuar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('reingreso.edit', session('estudiante_id')) }}?continuar=1";
            }
        });
    </script>
@endif
        <div class="box-header">
            <h1 class="box-title">Listado de Estudiantes para Reingreso</h1>
        </div>
        <div class="box-body">
            <table id="estudiantesTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Fotografía</th>
                        <th>Nombre</th>
                        <th>Carnet</th>
                        <th>Nivel</th>
                        <th>Grado</th>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudiantes as $estudiante)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}" alt="Fotografía" width="50" height="50">
                            </td>
                            <td>{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</td>
                            <td>{{ $estudiante->carnet }}</td>
                            <td>{{ $estudiante->cgshges->niveles->nivel ?? 'N/A' }}</td>
                            <td>{{ $estudiante->cgshges->grados->nombre ?? 'N/A' }}</td>
                            <td>{{ $estudiante->cgshges->cursos->curso ?? 'N/A' }}</td>
                            <td>{{ optional($estudiante->estado)->estado }}</td>
                            <td>

                                @php
                // Verificar si el estudiante está inscrito en el ciclo escolar activo
                $estaInscrito = $gestion
                    ? $estudiante->inscripciones->where('ciclo_escolar', $gestion->ciclo_escolar)->isNotEmpty()
                    : false;
            @endphp

            @if (!$gestion)
                <span class="label label-warning">Gestión no definida</span>
            @elseif (!$estaInscrito)
                <a href="{{ route('reingreso.edit', $estudiante->id) }}" class="btn btn-xs btn-primary" title="Matricular">
                    <i class="fa fa-refresh"></i> Matricular
                </a>
            @else
                <span class="label label-success">Ya inscrito en {{ $gestion->ciclo_escolar }}</span>
            @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('bottom')
        <script>
            $(document).ready(function() {
                $('#estudiantesTable').DataTable({
                    "searching": true,
                    "ordering": true,
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });
            });
        </script>
    @endpush
@endsection
