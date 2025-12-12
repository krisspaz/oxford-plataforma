@extends('crudbooster::admin_template')

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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Editar Contrato del Estudiante</h3>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('ajustes_contrato.update', $contrato->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

             <!-- Campo de Estudiante (solo lectura) -->
            <div class="form-group">
                <label for="estudiante_nombre">Estudiante</label>
                <input type="text" id="estudiante_nombre" class="form-control" 
                    value="{{ $contrato->estudiante->persona->nombres }} {{ $contrato->estudiante->persona->apellidos }}" readonly>
            </div>
            <input type="hidden" name="estudiante_id" value="{{ $contrato->estudiante_id }}">

           

                <!-- Botón para Descargar el Contrato Original -->
                @if($contrato->contrato_id)
                    @php
                        $contratoOriginal = $contratos->firstWhere('id', $contrato->contrato_id);
                    @endphp
                    @if($contratoOriginal)
                        <div class="form-group">
                            <label>Contrato Original</label>
                            <div>
                                <a href="{{ Storage::url($contratoOriginal->archivo) }}" class="btn btn-info" target="_blank">
                                    Descargar Contrato Original
                                </a>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Contrato Firmado -->
                <div class="form-group">
                    <label for="contrato_firmado">Contrato Firmado</label>

                    @if ($contrato->contrato_firmado)
                        <div class="mb-2">
                            <a href="{{ Storage::url($contrato->contrato_firmado) }}" class="btn btn-success" target="_blank">
                                Ver Contrato Firmado
                            </a>
                        </div>
                    @endif

                    <div>
                        <label for="contrato_firmado">Reemplazar Contrato Firmado:</label>
                        <input type="file" name="contrato_firmado" id="contrato_firmado" class="form-control"
                            accept="application/pdf">
                        <small class="form-text text-muted">Formatos permitidos: PDF</small>
                    </div>
                </div>

                <!-- Estado -->
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" id="estado" value="{{ $contrato->estado }}" class="form-control" required>
                </div>

                <!-- Botones -->
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('ajustes_contrato.index') }}" class="btn btn-secondary">Cancelar</a>

            </form>
        </div>
    </div>
@endsection
