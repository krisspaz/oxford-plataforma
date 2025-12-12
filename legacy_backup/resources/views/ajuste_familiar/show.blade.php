@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">Detalles de la Familia</h3>
        </div>
        <div class="box-body">
            <p><strong>Nombre:</strong> {{ $ajuste_familiar->nombre_familiar }}</p>
            <p><strong>Código:</strong> {{ $ajuste_familiar->codigo_familiar }}</p>
            <p><strong>Padre:</strong> {{ $ajuste_familiar->padre->nombres ?? '—' }}  {{ $ajuste_familiar->padre->apellidos ?? '—' }}</p>
            <p><strong>Madre:</strong> {{ $ajuste_familiar->madre->nombres ?? '—' }} {{ $ajuste_familiar->madre->apellidos ?? '—' }}</p>
            <p><strong>Encargado:</strong> {{ $ajuste_familiar->encargado->nombres ?? '—' }} {{ $ajuste_familiar->encargado->apellidos ?? '—' }}</p>
            <p><strong>Estudiante:</strong> {{ $ajuste_familiar->estudiante->persona->nombres ?? '—' }} {{ $ajuste_familiar->estudiante->persona->apellidos ?? '—' }}</p>
            <p><strong>Estado:</strong> {{ $ajuste_familiar->estado->estado ?? '—' }}</p>

            <a href="{{ route('ajuste-familiar.index') }}" class="btn btn-default">Volver</a>
        </div>
    </div>
@endsection
