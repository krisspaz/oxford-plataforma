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
        <h3 class="box-title">Buscar Estudiante o Familia</h3>
    </div>
    <div class="box-body">
        <form method="POST" action="{{ route('pagos.buscar') }}">
            @csrf
            <div class="form-group">
                <label for="criterio">Criterio de Búsqueda</label>
                <select class="form-control" id="criterio" name="criterio" required style="width: 25%">
                    <option value="">Seleccione un Criterio de Búsqueda</option>
                    <option value="estudiante">Estudiante</option>
                    <option value="familia">Familia</option>
                </select>
            </div>

            <div id="select-estudiante" class="form-group" style="display:none;">
                <label for="valor">Estudiante</label>
                <select class="form-control select2" id="valor" name="valor" style="width: 40%">
                    <option value="">Seleccione un estudiante</option>
                    @foreach($estudiantes as $estudiante)
                    <option value="{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}">
                           {{$estudiante->carnet}} {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="select-familia" class="form-group" style="display:none;">
                <label for="valorf">Familias</label>
                <select class="form-control select2" id="valorf" name="valorf" style="width: 40%">
                    <option value="">Seleccione Familia</option>
                  @foreach($familias->unique('codigo_familiar') as $familia)
                        <option value="{{ $familia->codigo_familiar }}">
                            {{ $familia->codigo_familiar }} {{ $familia->nombre_familiar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="Ciclo_escolar">Ciclo Escolar:</label>
                <select name="ciclo_escolar" id="ciclo_escolar" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un año</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}">{{ $anio }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const criterio = document.getElementById('criterio');
        const selectEstudiante = document.getElementById('select-estudiante');
        const selectFamilia = document.getElementById('select-familia');

        criterio.addEventListener('change', function() {
            if (this.value === 'estudiante') {
                selectEstudiante.style.display = 'block';
                selectFamilia.style.display = 'none';
            } else if (this.value === 'familia') {
                selectFamilia.style.display = 'block';
                selectEstudiante.style.display = 'none';
            } else {
                selectEstudiante.style.display = 'none';
                selectFamilia.style.display = 'none';
            }
        });
    });
</script>

@push('bottom')
<!-- Select2 CSS & JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4', // Opcional, puedes quitar si no usas Bootstrap 4
        placeholder: 'Seleccione',
        allowClear: true,
        theme: 'bootstrap4'
    });
});
</script>
@endpush

@endsection
