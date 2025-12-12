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
        <h1>Lista de Padres</h1>
        <a href="{{ route('padres.create') }}" class="btn btn-primary">Agregar Padre</a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Municipio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($padres as $padre)
                    <tr>
                        <td>{{ $padre->nombre }}</td>
                        <td>{{ $padre->apellido }}</td>
                        <td>{{ $padre->identificacionDocumento->nombre }}: {{ $padre->num_documento }}</td>
                        <td>{{ $padre->telefono }}</td>
                        <td>{{ $padre->municipio->municipio }}</td>
                        <td>
                            <a href="{{ route('padres.show', $padre->id) }}" class="btn btn-info">Ver</a>
                            <a href="{{ route('padres.edit', $padre->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('padres.destroy', $padre->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $padres->links() }}
    </div>
@endsection
