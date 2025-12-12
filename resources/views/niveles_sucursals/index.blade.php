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
        <h1>Niveles Sucursals</h1>
        <a href="{{ route('niveles_sucursals.create') }}" class="btn btn-primary">Crear Nivel Sucursal</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Nivel</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($niveles_sucursals as $niveles_sucursal)
                    <tr>
                        <td>{{ $niveles_sucursal->id }}</td>
                        <td>{{ $niveles_sucursal->sucursal->nombre_sucursal }}</td>
                        <td>{{ $niveles_sucursal->nivel->nombre }}</td>
                        <td>{{ $niveles_sucursal->estado->estado }}</td>
                        <td>
                            <a href="{{ route('niveles_sucursals.show', $niveles_sucursal->id) }}" class="btn btn-info">Ver</a>
                            <a href="{{ route('niveles_sucursals.edit', $niveles_sucursal->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('niveles_sucursals.destroy', $niveles_sucursal->id) }}" method="POST" style="display:inline-block;">
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
