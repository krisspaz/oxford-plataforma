@extends('crudbooster::admin_template')


@section('content')
<div class="container">
    <h1>Sucursales</h1>

    <a href="{{ route('sucursals.create') }}" class="btn btn-primary mb-3">Crear Nueva Sucursal</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Municipio</th>
                <th>Dirección</th>
                <th>Estado</th>
    
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sucursals as $sucursal)
                <tr>
                    <td>{{ $sucursal->nombre_sucursal }}</td>
                    <td>{{ $sucursal->municipio->municipio }}</td>
                    <td>{{ $sucursal->direccion }}</td>
                    <td>{{ $sucursal->estado->estado }}</td>
                  
                    <td>
                        <a href="{{ route('sucursals.show', $sucursal->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('sucursals.edit', $sucursal->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('sucursals.destroy', $sucursal->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sucursal?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay sucursales registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $sucursals->links() }}  <!-- Para la paginación -->
</div>
@endsection
