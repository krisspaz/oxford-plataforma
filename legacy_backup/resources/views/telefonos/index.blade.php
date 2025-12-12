@extends('crudbooster::admin_template')


@section('content')
    <h1>Teléfonos</h1>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('telefonos.create') }}">Crear Nuevo Teléfono</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Teléfono</th>
                
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($telefonos as $telefono)
                <tr>
                    <td>{{ $telefono->id }}</td>
                    <td>{{ $telefono->telefono }}</td>
                   
                    <td>
                        <a href="{{ route('telefonos.show', $telefono->id) }}">Ver</a>
                        <a href="{{ route('telefonos.edit', $telefono->id) }}">Editar</a>
                        <form action="{{ route('telefonos.destroy', $telefono->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este teléfono?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
