<!DOCTYPE html>
<html>
<head>
    <title>Editar Parentesco</title>
</head>
<body>
    <h1>Editar Parentesco</h1>

    <form action="{{ route('parentescos.update', $parentesco) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="parentesco">Nombre:</label>
        <input type="text" id="parentesco" name="parentesco" value="{{ $parentesco->parentesco }}" required>
        <button type="submit">Actualizar</button>
    </form>

    <a href="{{ route('parentescos.index') }}">Volver a la lista</a>
</body>
</html>
