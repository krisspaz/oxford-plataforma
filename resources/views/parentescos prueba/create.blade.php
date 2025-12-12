<!DOCTYPE html>
<html>
<head>
    <title>Crear Parentesco</title>
</head>
<body>
    <h1>Crear Parentesco</h1>

    <form action="{{ route('parentescos.store') }}" method="POST">
        @csrf
        <label for="parentesco">Nombre:</label>
        <input type="text" id="parentesco" name="parentesco" required>
        <button type="submit">Crear</button>
    </form>

    <a href="{{ route('parentescos.index') }}">Volver a la lista</a>
</body>
</html>
