@extends('crudbooster::admin_template')

@section('title', 'Inscripción de Alumnos')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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

@if (session('success'))
<div>
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ( Session::get('message') != '' )
<div class='alert alert-warning'>
    {{ Session::get('message') }}
</div>
@endif

<!-- Ver Nuevo Ingreso -->

<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <span class="info-box-icon btn-info">
            <i class="bi bi-file-person-fill text-white"></i> <!-- Ícono de color blanco -->
        </span>
        <div class="info-box-content ">
            <span class="info-box-text ">Inscripciones</span>
            <a href="{{ route('inscripcion.create') }}" class="btn btn-info btn-sm">
                <i class="bi bi-plus-square"></i> Nuevo Ingreso
            </a>
        </div>
    </div>
</div>


<!-- Ver Reingreso Ingreso -->

<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <span class="info-box-icon btn-primary">
            <i class="bi bi-person-lines-fill text-white"></i> <!-- Ícono de color blanco -->
        </span>
        <div class="info-box-content ">
            <span class="info-box-text ">Matricular</span>
            <a href="{{ route('reingreso.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-file-plus-fill"></i> Estudiantes
            </a>
        </div>
    </div>
</div>

<!-- Ver Familias -->
<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <span class="info-box-icon btn-success">
            <i class="bi bi-person-video2 text-white"></i> <!-- Ícono de color blanco -->
        </span>
        <div class="info-box-content">
            <span class="info-box-text">Familias</span>
            <a href="{{ route('familias.index') }}" class="btn btn-success btn-sm">
                <i class="bi bi-search"></i> Ver Familias
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: '¡Oops!',
            text: "{{ implode(', ', $errors->all()) }}",
            showConfirmButton: true
        });
    @endif

    @if (Session::get('message') != '')
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: "{{ Session::get('message') }}",
            showConfirmButton: true
        });
    @endif
</script>


@endsection


