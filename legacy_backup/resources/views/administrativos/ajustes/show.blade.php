@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Detalle del Administrativo</h3>
        </div>
        <div class="box-body">
            <p><strong>Nombre:</strong> {{ $administrativo->persona->nombres ?? '' }} {{ $administrativo->persona->apellidos ?? '' }}</p>
            <p><strong>Cargo:</strong> {{ $administrativo->cargo->nombre ?? '' }}</p>
            <p><strong>Estado:</strong> {{ $administrativo->estado->estado ?? '' }}</p>
            <p><strong>Fotografía:</strong></p>
            @if($administrativo->fotografia_administrativo)
                <img src="{{ asset('storage/' . $administrativo->fotografia_administrativo) }}" width="150">
            @endif
            <br><br>
            <a href="{{ route('ajuste-administrativos.index') }}" class="btn btn-default">Volver</a>
        </div>
    </div>
@endsection
