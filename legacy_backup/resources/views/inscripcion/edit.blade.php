@extends('crudbooster::admin_template')

@section('title', 'Inscripcion de ALumnos')


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
    <h1>Editar Inscripción</h1>

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulario de edición -->
    <form action="{{ route('inscripcion.update', $inscripcion->id) }}" method="POST">
        @csrf
        @method('PUT')

         
        @include('inscripcion.partials._form_padre')
        @include('inscripcion.partials._form_madre')
        @include('inscripcion.partials.form_encargado')
        @include('inscripcion.partials._form_alumno')
     
        
        <button type="submit" class="btn btn-primary">Actualizar Inscripción</button>
    </form>
    <a href="{{ route('inscripcion.index') }}" class="btn btn-success">Volver</a>
</div>

@parent
@include('inscripcion.partials._script')
@endsection
