<!-- resources/views/convenios/show.blade.php -->
@extends('crudbooster::admin_template')

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
    <h1>Detalles del Convenio</h1>

    <ul>
        <li><strong>Estudiante:</strong> {{ $convenio->inscripcion}}</li>
        <li><strong>Monto Total:</strong> {{ $convenio->monto_total }}</li>
        <li><strong>Cantidad de Cuotas:</strong> {{ $convenio->cantidad_cuotas }}</li>
        <li><strong>Fecha de Inicio:</strong> {{ $convenio->fecha_inicio }}</li>
        <li><strong>Fecha de Fin:</strong> {{ $convenio->fecha_fin }}</li>
    </ul>

    <a href="{{ route('convenios.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
