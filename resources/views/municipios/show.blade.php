@extends('crudbooster::admin_template')


@section('content')
    <h1>Detalles del Municipio</h1>

    <div>
        <strong>ID:</strong>
        <span>{{ $municipio->id }}</span>
    </div>
    <div>
        <strong>Municipio:</strong>
        <span>{{ $municipio->municipio }}</span>
    </div>
    <div>
        <strong>Departamento:</strong>
        <span>{{ $municipio->departamento->departamento }}</span>
    </div>
    <div>
        <strong>Estado:</strong>
        <span>{{ $municipio->estado->estado }}</span>
    </div>

    <a href="{{ route('municipios.index') }}">Volver</a>

 
    </form>
@endsection
