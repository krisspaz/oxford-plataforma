@extends('crudbooster::admin_template')


@section('content')
    <h1>Detalle Jornada-Dia-Horario</h1>

    <div>
        <p>ID: {{ $jornadaDiaHorario->id }}</p>
        <p>Jornada: {{ $jornadaDiaHorario->jornada->nombre }}</p>
        <p>Dia: {{ $jornadaDiaHorario->dia->nombre }}</p>
        <p>Horario: {{ $jornadaDiaHorario->horario->inicio }} - {{ $jornadaDiaHorario->horario->fin }}</p>
        <p>Estado: {{ $jornadaDiaHorario->estado->estado }}</p>
    </div>

    <a href="{{ route('jornada-dia-horarios.index') }}">Volver</a>
@endsection
