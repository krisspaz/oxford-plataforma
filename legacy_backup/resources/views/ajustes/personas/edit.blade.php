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
    <div class="box-header with-border">
        <h3 class="box-title">Editar Persona</h3>
    </div>

    <div class="box-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>¡Error!</strong> Por favor corrige los siguientes errores:<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ajuste-persona.update', $persona->id) }}" method="POST">
            @csrf
            @method('PUT')

            @include('ajustes.personas.partials.form', ['persona' => $persona])

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('ajuste-persona.index') }}" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</div>
@endsection
