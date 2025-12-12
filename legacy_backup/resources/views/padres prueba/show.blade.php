<!DOCTYPE html>
<html>
<head>
    <title>Detalles del Padre</title>
</head>
<body>
    <h1>Detalles del Padre</h1>

    <p>Nombre: {{ $padre->nombre }} {{ $padre->apellido }}</p>
    <p>Email: {{ $padre->email }}</p>
    <p>Teléfono: {{ $padre->telefono }}</p>
    <p>Dirección: {{ $padre->direccion }}</p>
    <p>Parentesco: {{ $padre->parentesco->parentesco }}</p>

    <a href="{{ route('padres.index') }}">Volver a la lista</a>
</body>
</html>
