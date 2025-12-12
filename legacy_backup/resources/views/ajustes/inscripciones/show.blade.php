@extends('crudbooster::admin_template')
@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Detalles de la Inscripción</h3>
        </div>

        <div class="box-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Estudiante:</strong> {{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}</li>
                <li class="list-group-item"><strong>Asignación:</strong> {{ $inscripcion->cgshges->grados->nombre }}{{ $inscripcion->cgshges->cursos->curso }} </li>
                <li class="list-group-item"><strong>Paquete:</strong> {{ $inscripcion->paquete->nombre }}</li>
                <li class="list-group-item"><strong>Fecha de Inscripción:</strong> {{ $inscripcion->fecha_inscripcion }}</li>
                <li class="list-group-item"><strong>Ciclo Escolar:</strong> {{ $inscripcion->ciclo_escolar }}</li>
                <li class="list-group-item"><strong>Estado:</strong> {{ $inscripcion->estado->estado }}</li>

            </ul>

            <a href="{{ route('ajustes_inscripciones.index') }}" class="btn btn-default">Volver al listado</a>
        </div>
    </div>

@endsection
