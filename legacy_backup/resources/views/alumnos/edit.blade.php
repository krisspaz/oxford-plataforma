@extends('crudbooster::admin_template')


@section('title', 'Alumnos')

@section('content')
<div class="container">
    <h1>Editar Alumno</h1>
    <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('alumnos.form', ['alumno' => $alumno])
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
