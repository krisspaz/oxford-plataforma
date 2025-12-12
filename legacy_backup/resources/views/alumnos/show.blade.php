@extends('crudbooster::admin_template')
@section('title', 'Alumnos')

@section('content')
<div class="container">
    <h1>Detalle del Alumno</h1>
    <table class="table">
        <tr>
            <th>ID</th>
            <td>{{ $alumno->id }}</td>
        </tr>
        <tr>
            <th>Código</th>
            <td>{{ $alumno->codigo }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $alumno->nombre }}</td>
        </tr>
        <tr>
            <th>Apellidos</th>
            <td>{{ $alumno->apellidos }}</td>
        </tr>
        <tr>
            <th>Género</th>
            <td>{{ $alumno->genero }}</td>
        </tr>
        <tr>
            <th>CUI</th>
            <td>{{ $alumno->cui }}</td>
        </tr>
        <tr>
            <th>Fecha de Nacimiento</th>
            <td>{{ $alumno->fecha_nacimiento }}</td>
        </tr>
        <tr>
            <th>Municipio</th>
            <td>{{ $alumno->municipio->municipio }}</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td>{{ $alumno->direccion }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $alumno->telefono }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $alumno->estado->estado }}</td>
        </tr>
    </table>
</div>
@endsection
