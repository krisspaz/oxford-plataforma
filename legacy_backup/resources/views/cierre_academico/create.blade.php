@extends('crudbooster::admin_template')

@section('content')

{{-- ✅ Alertas Bootstrap --}}
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

{{-- ✅ SweetAlert para mensajes --}}
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

{{-- ✅ Contenido principal --}}
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><b>Cierre Académico</b></h3>
    </div>

    <div class="box-body">
        <form id="formCierre" method="POST" action="{{ route('cierre_academico.store') }}">
            @csrf

            {{-- 🔹 Campo de selección de gestión --}}
            <div class="form-group">
                <label for="gestion_id">Seleccione la Gestión Académica</label>
                <select id="gestion_id" name="gestion_id" class="form-control select2" required>
                    <option value="">-- Seleccione una gestión activa --</option>
                    @foreach($gestiones as $gestion)
                        <option value="{{ $gestion->id }}">{{ $gestion->gestion }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Botón de confirmación --}}
            <div class="text-center mt-4">
                <button type="button" id="btnCerrarGestion" class="btn btn-danger btn-lg">
                    <i class="fa fa-lock"></i> Cerrar Gestión Académica
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Script para activar Select2 y confirmar el envío --}}

<script>
    $(document).ready(function () {
        // Inicializar Select2
        $('.select2').select2({
            placeholder: "Seleccione una gestión activa",
            allowClear: true
        });

        // Confirmación antes de enviar
        $('#btnCerrarGestion').click(function (e) {
            e.preventDefault();
            let gestionText = $('#gestion_id option:selected').text();

            if (!$('#gestion_id').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Por favor seleccione una gestión antes de continuar.',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            Swal.fire({
                title: '¿Está seguro?',
                html: `Está a punto de <b>cerrar la gestión "${gestionText}"</b>. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cerrar gestión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formCierre').submit();
                }
            });
        });
    });
</script>


@endsection
