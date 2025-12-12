
<!-- resources/views/sucursal_telefonos/index.blade.php -->

@extends('crudbooster::admin_template')

@section('content')
    <div class="container">
        <h1>Relaciones Sucursal-Telefono</h1>
        <a href="{{ route('sucursal_telefonos.create') }}" class="btn btn-primary mb-3">Crear Nueva Relación</a>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Telefono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sucursalTelefonos as $sucursalTelefono)
                    <tr>
                        <td>{{ $sucursalTelefono->id }}</td>
                        <td>{{ $sucursalTelefono->sucursal->nombre_sucursal }}</td>
                        <td>{{ $sucursalTelefono->telefono->telefono }}</td>
                        <td>
                            <a href="{{ route('sucursal_telefonos.show', $sucursalTelefono->id) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('sucursal_telefonos.edit', $sucursalTelefono->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('sucursal_telefonos.destroy', $sucursalTelefono->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $sucursalTelefonos->links() }}
    </div>
@endsection
