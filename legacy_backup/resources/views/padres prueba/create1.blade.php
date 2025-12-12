<!DOCTYPE html>
<html>
<head>
    <title>Crear Padre</title>
</head>
<body>
    <h1>Crear Padre</h1>

    <form action="{{ route('padres.store') }}" method="POST">
        @csrf
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion">
        <label for="parentesco_id">Parentesco:</label>
        <select id="parentesco_id" name="parentesco_id" required>
            @foreach ($parentescos as $parentesco)
                <option value="{{ $parentesco->id }}">{{ $parentesco->parentesco }}</option>
            @endforeach
        </select>
        <button type="submit">Crear</button>
    </form>

    <a href="{{ route('padres.index') }}">Volver a la lista</a>
</body>
</html>
