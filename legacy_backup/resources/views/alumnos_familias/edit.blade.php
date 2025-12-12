@extends('crudbooster::admin_template')

@section('title', 'Alumnos y Familias')



@section('content')
<div class="container">
    <h1>Editar Familia</h1>
    @include('alumnos_familias.form', ['action' => route('alumnos_familias.update', $alumnosFamilia->id), 'method' => 'PUT'])
</div>
@endsection
