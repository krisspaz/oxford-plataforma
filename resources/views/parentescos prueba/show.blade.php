<!DOCTYPE html>
<html>
<head>
    <title>Detalles del Parentesco</title>
</head>
<body>
    <h1>Detalles del Parentesco</h1>

    <p>Parentesco: {{ $parentesco->parentesco }}</p>

    <a href="{{ route('parentescos.index') }}">Volver a la lista</a>
</body>
</html>
