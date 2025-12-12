@extends('crudbooster::admin_template')

@section('title', 'Listado de Personas')

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

    <h1>Lista de Personas</h1>
    <a href="{{ route('personas.create') }}">Crear Persona</a>

    <table>
        <thead>
            <tr>
                <th>Fotografía</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personas as $persona)
                <tr>
                    <td><img src="{{ asset('storage/fotografias/' . $persona->fotografia) }}" alt="" width="50"></td>
                    <td>{{ $persona->nombres }} {{ $persona->apellidos }}</td>
                    <td>{{ $persona->email }}</td>
                    <td>
                        <a href="{{ route('personas.show', $persona) }}">Ver</a>
                        <a href="{{ route('personas.edit', $persona) }}">Editar</a>
                        <form action="{{ route('personas.destroy', $persona) }}" method="POST" style="display:inline;">
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
