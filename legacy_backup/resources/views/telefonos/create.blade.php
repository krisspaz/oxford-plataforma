@extends('crudbooster::admin_template')



@section('content')
    <h1>Crear Nuevo Teléfono</h1>

    <form action="{{ route('telefonos.store') }}" method="POST">
        @csrf
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" >
        </div>
       
        <button type="submit">Guardar</button>
    </form>
@endsection
