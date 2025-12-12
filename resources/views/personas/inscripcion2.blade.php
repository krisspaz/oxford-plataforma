@extends('crudbooster::admin_template')

@section('title', 'Crear Registro')


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
<div class="container">
    <h2>Registrar Padre, Madre y Encargado</h2>
    
    <form action="{{ route('personas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Pestañas para organizar las secciones -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="padre-tab" data-toggle="tab" href="#padre" role="tab" aria-controls="padre" aria-selected="true">Información del Padre</a>
            </li>
           
           
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="direccion-tab" data-toggle="tab" href="#direccion" role="tab" aria-controls="direccion" aria-selected="false">Dirección Domiciliar</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">

            <!-- Sección para el Padre -->
            <div class="tab-pane fade show active" id="padre" role="tabpanel" aria-labelledby="padre-tab">
                <h4>Datos del Padre</h4>
                
                @include('personas.extensiones.formdb', ['prefix' => 'padre'])

            </div>

           

         


             <!-- Direccion -->
             <div class="tab-pane fade" id="direccion" role="tabpanel" aria-labelledby="direccion-tab">
                <h4>Datos del Encargado</h4>

                @include('personas._formdireccin')
            </div>

        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
    </form>
</div>

@endsection

<!-- Script para manejo de pestañas -->
@push('scripts')
<script>
    $(function () {
        $('#myTab a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
@endpush
