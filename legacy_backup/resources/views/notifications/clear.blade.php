@extends('crudbooster::admin_template')
@section('title', 'Eliminar Notificaciones')

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

<div class="row">
    <div class="col-md-12">
        <div class="box">

            <div class="box-header">
                <h3 class="box-title">Eliminar todas las notificaciones</h3>
            </div>

            <div class="box-body">

                <p class="text-danger" style="font-size:16px;">
                    Esta acción eliminará <strong>TODAS las notificaciones</strong> del sistema.
                </p>

                <button id="btnEliminar" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Eliminar todas las notificaciones
                </button>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('btnEliminar').addEventListener('click', function () {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción eliminará TODAS las notificaciones y no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ CRUDBooster::adminPath('notifications/clear-all') }}";
        }
    });
});
</script>

@endsection
