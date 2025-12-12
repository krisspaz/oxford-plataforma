@extends('crudbooster::admin_template')



@section('content')
<!-- resources/views/recibos/exito.blade.php -->

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
    <h2>Recibo Emitido Exitosamente</h2>
    <p>El Recibo ha sido certificada con éxito.</p>
    <p><strong>Document GUID:</strong> {{ session('guid') }}</p>
    <p><strong>Serie:</strong> {{ session('serie') }}</p>
    <p><strong>Número:</strong> {{ session('numero') }}</p>
    <p>
        <strong>Link del Recibo:</strong>
        <a href="{{ route('recibo.descargar', ['path' => session('link')]) }}" class="btn btn-primary">Descargar Recibo</a>

    </p>
@endsection


