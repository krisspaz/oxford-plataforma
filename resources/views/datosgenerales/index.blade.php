@extends('crudbooster::admin_template')

@section('title', 'Datos Generales')

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
        <h1>Familias</h1>
        <a href="{{ route('datosgenerales.create') }}" class="btn btn-primary">Crear Nueva Familia</a>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código Familiar</th>
                    <th>Padre</th>
                    <th>Madre</th>
                    <th>Encargado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($familias as $familia)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $familia->codigofamiliar }}</td>
                        <td>{{ $familia->padre->nombre }} {{ $familia->padre->apellido }}</td>
                        <td>{{ $familia->madre->nombre }} {{ $familia->madre->apellido }}</td>
                        <td>{{ $familia->encargado->nombre }} {{ $familia->encargado->apellido }}</td>
                        <td>
                            <a href="{{ route('datosgenerales.show', $familia->id) }}" class="btn btn-info">Ver</a>
                            <a href="{{ route('datosgenerales.edit', $familia->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('datosgenerales.destroy', $familia->id) }}" method="POST" style="display:inline;">
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
