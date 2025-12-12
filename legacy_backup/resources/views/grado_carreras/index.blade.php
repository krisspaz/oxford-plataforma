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
    <h1>Grado-Carreras</h1>
    <a href="{{ route('grado-carreras.create') }}">Crear Grado-Carrera</a>

    @if ($message = Session::get('success'))
        <div>
            {{ $message }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Grado</th>
                <th>Carrera</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gradoCarreras as $gradoCarrera)
                <tr>
                    <td>{{ $gradoCarrera->id }}</td>
                    <td>{{ $gradoCarrera->grado->nombre }}</td>
                    <td>{{ $gradoCarrera->carrera->nombre }}</td>
                    <td>{{ $gradoCarrera->estado->estado }}</td>
                    <td>
                        <a href="{{ route('grado-carreras.show', $gradoCarrera->id) }}">Ver</a>
                        <a href="{{ route('grado-carreras.edit', $gradoCarrera->id) }}">Editar</a>
                        <form action="{{ route('grado-carreras.destroy', $gradoCarrera->id) }}" method="POST" style="display:inline-block;">
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
