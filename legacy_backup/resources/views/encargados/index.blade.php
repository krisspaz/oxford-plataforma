@extends('crudbooster::admin_template')

@section('title', 'Encargados')

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
    <h1>Encargado</h1>
    <a href="{{ route('encargados.create') }}">Crear Encargado</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Parentesco</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Documento de Identificación</th>
                <th>Número de Documento</th>
                <th>Fecha de Nacimiento</th>
                <th>Profesión</th>
                <th>Teléfono</th>
                <th>Municipio</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($encargados as $encargado)
                <tr>
                    <td>{{ $encargado->id }}</td>
                    <td>{{ $encargado->parentesco->parentesco }}</td>
                    <td>{{ $encargado->nombre }}</td>
                    <td>{{ $encargado->apellido }}</td>
                    <td>{{ $encargado->identificacionDocumento->nombre }}</td>
                    <td>{{ $encargado->num_documento }}</td>
                    <td>{{ $encargado->fecha_nacimiento }}</td>
                    <td>{{ $encargado->profesion }}</td>
                    <td>{{ $encargado->telefono }}</td>
                    <td>{{ $encargado->municipio->municipio }}</td>
                    <td>{{ $encargado->direccion }}</td>
                    <td>
                        <a href="{{ route('encargados.show', $encargado->id) }}">Mostrar</a>
                        <a href="{{ route('encargados.edit', $encargado->id) }}">Editar</a>
                        <form action="{{ route('encargados.destroy', $encargado->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
