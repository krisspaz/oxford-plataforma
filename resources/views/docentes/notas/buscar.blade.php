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
        <h3 class="box-title">Buscar Notas por Estudiante y Ciclo Escolar</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ route('cuadro-notas.notaspadres') }}">
            <div class="form-group">
                <label for="estudiante_id">Estudiante</label>
                <select name="estudiante_id" class="form-control select2" required>
                    <option value="">Seleccione un estudiante</option>
                    @foreach($estudiantes as $e)
                        <option value="{{ $e->id }}"> {{ $e->persona->apellidos }} {{ $e->persona->nombres }} {{"("}} {{ $e->carnet }} {{")"}}</option>
                    @endforeach
                </select>
            </div>

               <div class="form-group">
                <label for="promedio">Tipo de Promedio</label>
                <select name="promedio" class="form-control" required style="width: 30%">
                    <option value="">Seleccione el Tipo de Promedio </option>

                        <option value="puntuacion">{{ "Nota Bimestral" }}</option>
                         <option value="porcentaje">{{ "Porcentaje Bimestral" }}</option>
                            <option value="mixto">{{ "Nota Bimestral con Promedio Porcentual" }}</option>

                </select>
            </div>

            <div class="form-group">
                <label for="ciclo_escolar">Ciclo Escolar</label>
                <select name="ciclo_escolar" class="form-control" required style="width: 30%">
                    <option value="">Seleccione un ciclo escolar</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo }}">{{ $ciclo }}</option>
                    @endforeach
                </select>
            </div>



            {{-- NUEVO CAMPO CHECKBOX --}}
            <div class="form-group mt-3">
                <div class="form-check">
                    <input type="checkbox" name="incluir_insolventes" id="incluir_insolventes" class="form-check-input" value="1">
                    <label for="incluir_insolventes" class="form-check-label">
                        Incluir insolventes (mostrar aunque tenga pagos pendientes)
                    </label>
                </div>
            </div>




            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
</div>

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
