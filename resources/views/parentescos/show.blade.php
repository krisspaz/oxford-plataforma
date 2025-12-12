@extends('crudbooster::admin_template')



@section('content')
    <div class="container">
        <h1>Detalles del Parentesco</h1>
        
        <div class="form-group">
            <label>Parentesco:</label>
            <p>{{ $parentesco->parentesco }}</p>
        </div>
        
        <div class="form-group">
            <label>Estado:</label>
            <p>{{ $parentesco->estado->estado }}</p>
        </div>
        
        <a href="{{ route('parentescos.edit', $parentesco->id) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('parentescos.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
