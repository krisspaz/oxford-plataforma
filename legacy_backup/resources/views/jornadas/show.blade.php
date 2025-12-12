@extends('crudbooster::admin_template')



@section('content')
    <h1>Detalle de la Jornada</h1>
    <div>
        <strong>ID:</strong> {{ $jornada->id }}
    </div>
    <div>
        <strong>Nombre:</strong> {{ $jornada->nombre }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $jornada->estado->estado }}
    </div>
    <a href="{{ route('jornadas.index') }}">Volver</a>
@endsection
