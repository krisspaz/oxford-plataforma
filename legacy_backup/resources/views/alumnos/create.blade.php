@extends('crudbooster::admin_template')


@section('content')
<div class="container">
    <h1>Crear Alumno</h1>
    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf
        @include('alumnos.form')
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
