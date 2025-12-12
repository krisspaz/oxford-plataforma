@extends('crudbooster::admin_template')



@section('title', 'Municipio List')

@section('content')
    <title>Lista de Alumnos</title>

    <h1>Lista de Alumnos</h1>
    <a href="{{ route('alumnos.create') }}">Crear Nuevo Alumno</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($alumnos as $alumno)
            <tr>
                <td> {{ $alumno->id }} </td>
                <td> {{ $alumno->codigo }} </td>
                <td> {{ $alumno->nombre }} </td>
                <td>{{ $alumno->apellido }}</td>
                <td>
                <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-info">Ver</a>
                <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning">Editar</a>
                <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endsection
