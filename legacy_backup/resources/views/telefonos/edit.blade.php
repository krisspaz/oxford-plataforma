@extends('crudbooster::admin_template')


@section('content')
    <h1>Editar Teléfono</h1>

    <form action="{{ route('telefonos.update', $telefono->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="{{ $telefono->telefono }}" >
        </div>
       
        <button type="submit">Actualizar</button>
    </form>
@endsection
