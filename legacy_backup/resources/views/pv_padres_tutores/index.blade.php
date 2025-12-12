@extends('crudbooster::admin_template')

@section('title', 'Tutores')

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
    <h1>Lista de Padres y Tutores</h1>
    <a href="{{ route('pv_padres_tutores.create') }}" class="btn btn-primary">Crear Nuevo</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Código Familiar</th>
                <th>Padre</th>
                <th>Madre</th>
                <th>Encargado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($padres_tutores as $padres_tutor)
            <tr>
                <td>{{ $padres_tutor->codigofamiliar }}</td>
                <td>{{ optional($padres_tutor->padre)->nombre }}</td>
                <td>{{ optional($padres_tutor->madre)->nombre }}</td>
                <td>{{ optional($padres_tutor->encargado)->nombre }}</td>
                <td>
                    <a href="{{ route('pv_padres_tutores.show', $padres_tutor->id) }}" class="btn btn-info">Ver</a>
                    <a href="{{ route('pv_padres_tutores.edit', $padres_tutor->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('pv_padres_tutores.destroy', $padres_tutor->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
