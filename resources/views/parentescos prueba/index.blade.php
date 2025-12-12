<!DOCTYPE html>
<html>
<head>
    <title>Lista de Parentescos</title>
</head>
<body>
    <h1>Lista de Parentescos</h1>
    <a href="{{ route('parentescos.create') }}">Crear Nuevo Parentesco</a>

    <ul>
        @foreach ($parentescos as $parentesco)
            <li>
                {{ $parentesco->parentesco }}
                <a href="{{ route('parentescos.show', $parentesco) }}">Ver</a>
                <a href="{{ route('parentescos.edit', $parentesco) }}">Editar</a>
                <form action="{{ route('parentescos.destroy', $parentesco) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
