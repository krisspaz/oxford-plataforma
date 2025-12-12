@extends('crudbooster::admin_template')

@section('content')

{{-- ✅ Mensajes de éxito o error --}}
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

{{-- 🔹 Título --}}
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Gestiones Académicas Activas</h3>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre de la Gestión</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ngestiones as $index => $gestion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $gestion->gestion }}</td>
                    <td>{{ $gestion->ciclo_escolar ?? 'N/A' }}</td>
                    <td><span class="label label-success">Activo</span></td>
                    <td>
                        <button class="btn btn-danger btn-sm"
                            data-toggle="modal"
                            data-target="#modalCierre"
                            data-id="{{ $gestion->id }}"
                           data-nombre="{{ $gestion->ciclo_escolar . '-' . $gestion->gestion }}">
                            <i class="fa fa-lock"></i> Cerrar Gestión
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- 🔹 Modal de Cierre Académico --}}
<div class="modal fade" id="modalCierre" tabindex="-1" role="dialog" aria-labelledby="modalCierreLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('cierre_academico.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h4 class="modal-title" id="modalCierreLabel">Cierre de Gestión Académica</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="gestion_id" id="gestion_id">
                <p>¿Está seguro de cerrar la gestión <strong id="gestion_nombre"></strong>?</p>

                <div class="form-group mt-3">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="migrar" id="migrar_checkbox" value="1">
                            Migrar Estructura Académica
                        </label>
                    </div>
                </div>

                <div class="form-group" id="migrarSelectContainer" style="display:none;">
                    <label for="migrar_a">Migrar a la siguiente gestión:</label>
                    <select name="migrar_a" id="migrar_a" class="form-control">
                        <option value="">Seleccione una gestión destino</option>
                        @foreach($ngestiones as $gestion)
                            <option value="{{ $gestion->id }}">{{ $gestion->ciclo_escolar }}{{ "-" }}{{ $gestion->gestion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Cerrar Gestión</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Cargar datos en el modal
    $('#modalCierre').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var gestionId = button.data('id');
        var gestionNombre = button.data('nombre');

        var modal = $(this);
        modal.find('#gestion_id').val(gestionId);
        modal.find('#gestion_nombre').text(gestionNombre);

        // Filtrar la gestión actual en el select
        modal.find('#migrar_a option').each(function() {
            if ($(this).val() == gestionId) {
                $(this).hide(); // Oculta la gestión actual
            } else {
                $(this).show();
            }
        });
    });

    // ✅ Mostrar u ocultar el select cuando se marca el checkbox
    $('#migrar_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#migrarSelectContainer').slideDown();
        } else {
            $('#migrarSelectContainer').slideUp();
            $('#migrar_a').val('');
        }
    });
});
</script>
@endsection
