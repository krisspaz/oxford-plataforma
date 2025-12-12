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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Mensajes SweetAlert --}}
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
        <h3 class="box-title">Listado de Documentos de Inscripción</h3>
        <div class="box-tools">
            <a href="{{ route('documentos.create') }}" class="btn btn-success btn-sm">Agregar Documento</a>
        </div>
    </div>

    <div class="box-body">
        {{-- Checkbox para incluir documentos expirados --}}
        <form method="GET" action="{{ route('documentos.index') }}" class="mb-3">
            <label class="inline-flex items-center">
                <input type="checkbox" name="incluir_expirados" value="1"
                       onchange="this.form.submit()"
                       {{ $incluirExpirados ? 'checked' : '' }}>
                <span class="ml-2">Incluir Documentos Expirados</span>
            </label>
        </form>

        {{-- Buscador --}}
        <div class="form-group">
            <input type="text" id="buscador-estudiante" class="form-control" placeholder="Buscar estudiante por nombre...">
        </div>

        @php
            use Carbon\Carbon;

            $hoy = Carbon::now()->startOfDay();
            $documentosAgrupados = collect($documentos)->groupBy(function($doc) {
                return $doc->estudiante->persona->nombres . ' ' . $doc->estudiante->persona->apellidos;
            });
        @endphp

        <div id="lista-estudiantes">
            @foreach($documentosAgrupados as $estudianteNombre => $docs)
                <div class="panel panel-default estudiante-panel" data-nombre="{{ strtolower($estudianteNombre) }}">
                    <div class="panel-heading">
                        <strong>{{ $estudianteNombre }}</strong>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                     <th>Carné</th>
                                    <th>Estudiante</th>
                                    <th>Tipo</th>
                                    <th>Nombre del Documento</th>
                                    <th>Expiración</th>
                                    <th>Vigencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docs as $doc)
                                    @php
                                        $expiracion = $doc->fexpiracion ? Carbon::parse($doc->fexpiracion) : null;
                                        $vigente = !$expiracion || $expiracion->gte($hoy);
                                    @endphp

                                    {{-- Solo mostrar vigentes o todos si se marcó el checkbox --}}
                                    @if($vigente || $incluirExpirados)
                                        <tr>
                                             <td>{{ $doc->estudiante->carnet }}</td>
                                             <td>{{ $doc->estudiante->persona->apellidos }}{{ " " }}{{ $doc->estudiante->persona->nombres }}</td>
                                            <td>{{ $doc->tipo_documento }}</td>
                                            <td>{{ $doc->nombre_documento }}</td>
                                            <td>{{ $doc->fexpiracion ? $expiracion->format('d/m/Y') : 'Sin fecha' }}</td>
                                            <td>
                                                @if($vigente)
                                                    <span class="label label-success">Vigente</span>
                                                @else
                                                    <span class="label label-danger">Expirado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('documentos.show', $doc->id) }}" class="btn btn-xs btn-info">Ver</a>
                                                <a href="{{ route('documentos.edit', $doc->id) }}" class="btn btn-xs btn-warning">Editar</a>
                                                <form action="{{ route('documentos.destroy', $doc->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este documento?')">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Script del buscador --}}
@push('bottom')
<script>
    document.getElementById('buscador-estudiante').addEventListener('keyup', function() {
        let valor = this.value.toLowerCase();
        let paneles = document.querySelectorAll('.estudiante-panel');

        paneles.forEach(function(panel) {
            let nombre = panel.getAttribute('data-nombre');
            panel.style.display = nombre.includes(valor) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
