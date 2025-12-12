@extends('crudbooster::admin_template')



@section('content')
    <h1>Detalle del Horario</h1>
    <div>
        <strong>ID:</strong> {{ $horario->id }}
    </div>
    <div>
        <strong>Inicio:</strong> {{ $horario->inicio }}
    </div>
    <div>
        <strong>Fin:</strong> {{ $horario->fin }}
    </div>
    <div>
        <strong>Estado:</strong> {{ $horario->estado->estado }}
    </div>
    <a href="{{ route('horarios.index') }}">Volver</a>
@endsection
