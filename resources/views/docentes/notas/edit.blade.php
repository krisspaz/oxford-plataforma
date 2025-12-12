@extends('crudbooster::admin_template')

@section('content')
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
        <h3 class="box-title">Editar Nota Final</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('cuadro-notas.update', $nota->id) }}" method="POST">
            @csrf
            @method('PUT')
         
            <div class="form-group">
                <label for="nota_acumulada">Nota Final del Bimestre</label>
                <input type="number" name="nota_acumulada" class="form-control" min="0" max="100" step="0.01" value="{{ $nota->nota_acumulada }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Nota</button>
        </form>
    </div>
</div>
@endsection
