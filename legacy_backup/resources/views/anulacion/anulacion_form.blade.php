@extends('crudbooster::admin_template')
@section('content')

<div class="box box-default">

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
    <div class="box-header with-border">
        <h3 class="box-title">Anular Documento</h3>
    </div>

    @if(Session::has('message'))
    <div class="alert alert-{{ Session::get('message_type', 'info') }} alert-dismissible fade show" role="alert">
        {{ Session::get('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <form id="form-anular" method="POST" action="{{ route('anulador.enviar') }}">
        @csrf
        <div class="box-body">
            <div class="form-group">
                <label for="numero_documento">Número del Documento a Anular</label>
                <input type="text" class="form-control" id="numero_documento" name="numero_documento" placeholder="Ej. 4EDD7A69-8846-4CC5-9199-683CD102AB9E" required value="{{ old('numero_documento') }}" style="width: 30%">
            </div>

            <div class="form-group">
                <label for="nit_receptor">NIT del Receptor</label>
                <input type="text" class="form-control" id="nit_receptor" name="nit_receptor" placeholder="CF"  required value="{{ old('nit_receptor') }}" style="width: 30%">
            </div>

            <div class="form-group">
                <label for="fecha_emision">Fecha de Emisión del Documento a Anular</label>
                <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" required value="{{ old('fecha_emision') }}" style="width: 30%">
            </div>

            <div class="form-group">
                <label for="motivo">Motivo de Anulación</label>
                <textarea class="form-control" id="motivo" name="motivo" rows="3" required required value="{{ old('motivo') }}" style="width: 30%"></textarea>
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-danger" id="btn-anular">
                <i class="fa fa-times-circle"></i> Anular Documento
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('btn-anular').addEventListener('click', function(e) {
        e.preventDefault(); // Detener envío inmediato
    
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-anular').submit(); // Enviar el formulario
            }
        });
    });
    </script>

@endsection
