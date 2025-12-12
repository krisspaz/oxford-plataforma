@extends('crudbooster::admin_template')

@push('head')
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<!-- Box para agregar documento -->
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
        @if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups! Hubo algunos errores:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <h3 class="box-title">Agregar Documento</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Formulario para agregar documentos -->
            @include('documentos.form')  <!-- Aquí debes incluir el formulario de los campos de tu modelo DocumentoInscripcion -->

           
            <!-- Botones de guardar y cancelar -->
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="{{ route('documentos.index') }}" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</div>




@endsection
