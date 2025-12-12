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
    <h1>Detalles de Paquetes</h1>
    <a href="{{ route('paquete_detalles.create') }}" class="btn btn-success mb-3">Crear Nuevo Detalle</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Paquete</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $index => $detalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detalle->paquete->nombre ?? 'No asignado' }}</td>
                    <td>{{ $detalle->nombre }}</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td>Q.{{ number_format($detalle->precio, 2) }}</td>
                    <td>
                        <a href="{{ route('paquete_detalles.show', $detalle->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('paquete_detalles.edit', $detalle->id) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('paquete_detalles.destroy', $detalle->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este detalle?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Aquí directo --}}



@endsection
