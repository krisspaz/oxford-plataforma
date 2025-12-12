@extends('crudbooster::admin_template')

<!-- resources/views/telefonos/show.blade.php -->



@section('content')
    <div class="container">
        <h1>Detalle del Teléfono</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><strong>Telefono:</strong>{{ $telefono->telefono }}</h5>
               
               
                <a href="{{ route('telefonos.index') }}" class="btn btn-secondary">Volver al listado</a>
                 <a href="{{ route('telefonos.edit', $telefono->id) }}" class="btn btn-warning">Editar</a>
                
                 <form action="{{ route('telefonos.destroy', $telefono->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>

       
       
    </div>
@endsection
