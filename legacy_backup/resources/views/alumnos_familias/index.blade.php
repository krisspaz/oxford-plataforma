@extends('crudbooster::admin_template')

@section('title', 'Alumnos y Familias')


@section('content')
<div class="container">
    <h1>Familias de Alumnos</h1>
    <a href="{{ route('alumnos_familias.create') }}" class="btn btn-primary">Agregar Familia</a>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Alumno</th>
                <th>Codigo Familiar</th>
                <th>Tutor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnosFamilias as $familia)
                <tr>
                    <td>{{ $familia->id }}</td>
                    <td>{{ $familia->alumno->nombre }}</td>
                    <td>{{ $familia->padresTutores->codigofamiliar }}</td>
                    <td>{{ $familia->padresTutores->encargado->nombre }} {{" "}} {{ $familia->padresTutores->encargado->apellido }}</td>
                    <td>
                        <a href="{{ route('alumnos_familias.show', $familia->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('alumnos_familias.edit', $familia->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('alumnos_familias.destroy', $familia->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
