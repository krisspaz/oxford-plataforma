@extends('layouts.app')

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
        <h1>Detalle del Pago</h1>

        <p><strong>Estudiante:</strong> {{ $pago->estudiante->nombre }}</p>
        <p><strong>Monto:</strong> {{ $pago->monto }}</p>
        <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago }}</p>
        <p><strong>Método de Pago:</strong> {{ $pago->metodo_pago }}</p>

        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
@endsection
