<!DOCTYPE html>
<html>
<head>
    <title>Editar Padre</title>
</head>
<body>
    <h1>Editar Padre</h1>

    <form action="{{ route('padres.update', $padre) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="{{ $padre->nombre }}" required>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="{{ $padre->apellido }}" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ $padre->email }}" required>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="{{ $padre->telefono }}">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="{{ $padre->direccion }}">
        <label for="parentesco_id">Parentesco:</label>
        <select id="parentesco_id" name="parentesco_id" required>
            @foreach ($parentescos as $parentesco)
                <option value="{{ $parentesco->id }}" {{ $padre->parentesco_id == $parentesco->id ? 'selected' : '' }}>
                    {{ $parentesco->parentesco }}
                </option>
            @endforeach
        </select>
        <button type="submit">Actualizar</button>
    </form>

    <a href="{{ route('padres.index') }}">Volver a la lista</a>
</body>
</html>
