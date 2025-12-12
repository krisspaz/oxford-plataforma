@extends('crudbooster::admin_template')



@section('content')
    <h1>Madres</h1>
    <a href="{{ route('madres.create') }}">Crear Madre</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
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
            @foreach ($madres as $madre)
                <tr>
                    <td>{{ $madre->id }}</td>
                    <td>{{ $madre->nombre }}</td>
                    <td>{{ $madre->apellido }}</td>
                    <td>{{ $madre->identificacionDocumento->nombre }}</td>
                    <td>{{ $madre->num_documento }}</td>
                    <td>{{ $madre->fecha_nacimiento }}</td>
                    <td>{{ $madre->profesion }}</td>
                    <td>{{ $madre->telefono }}</td>
                    <td>{{ $madre->municipio->municipio }}</td>
                    <td>{{ $madre->direccion }}</td>
                    <td>
                        <a href="{{ route('madres.show', $madre->id) }}">Mostrar</a>
                        <a href="{{ route('madres.edit', $madre->id) }}">Editar</a>
                        <form action="{{ route('madres.destroy', $madre->id) }}" method="POST" style="display:inline;">
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
