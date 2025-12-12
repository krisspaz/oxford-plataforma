@extends('layouts.app')

@section('content')


@endif
<div class="container">
    <h1>Nueva Matriculación</h1>
    <form action="{{ route('matriculaciones.store') }}" method="POST">
        @csrf
        @include('matriculaciones._form', ['matriculacion' => null])
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('matriculaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
