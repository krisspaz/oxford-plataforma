<!DOCTYPE html>
<html>
<head>
    <title>Lista de Padres</title>
</head>
<body>
    <h1>Lista de Padres</h1>
    <a href="{{ route('padres.create') }}">Crear Nuevo Padre</a>

    <ul>
        @foreach ($padres as $padre)
            <li>
                {{ $padre->nombre }} {{ $padre->apellido }}
                <a href="{{ route('padres.show', $padre) }}">Ver</a>
                <a href="{{ route('padres.edit', $padre) }}">Editar</a>
                <form action="{{ route('padres.destroy', $padre) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
