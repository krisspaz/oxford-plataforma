@extends('crudbooster::admin_template')



@section('title', 'Sucursales List')

@section('content')
    <h1>Sucursales List</h1>
    <a href="{{ route('sucursales.create') }}" class="btn btn-primary">Add New Sucursal</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Sucursal</th>
                <th>Municipio</th>
                <th>Direccion</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sucursales as $sucursale)
                <tr>
                    <td>{{ $sucursale->id }}</td>
                    <td>{{ $sucursale->nombre_sucursal }}</td>
                    <td>{{ $sucursale->municipio->municipio }}</td>
                    <td>{{ $sucursale->direccion }}</td>
                    <td>{{ $sucursale->status->status_name }}</td>
                    <td>
                        <a href="{{ route('sucursales.show', $sucursale) }}" class="btn btn-info">View</a>
                        <a href="{{ route('sucursales.edit', $sucursale) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('sucursales.destroy', $sucursale) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
