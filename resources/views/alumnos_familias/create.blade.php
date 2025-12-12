@extends('crudbooster::admin_template')

@section('title', 'Alumnos y Familias')


@section('content')
<div class="container">
    <h1>Agregar Familia</h1>
    @include('alumnos_familias.form', ['action' => route('alumnos_familias.store'), 'method' => 'POST'])
</div>
@endsection
