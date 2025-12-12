@extends('crudbooster::admin_template')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Detalles del Contrato Estudiantil</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="estudiante_id">Estudiante</label>
                <p>{{ $ajusteContrato->estudiante->persona->nombres }} {{ $ajusteContrato->estudiante->persona->apellidos }}</p>
            </div>

            <div class="form-group">
                <label for="contrato_id">Contrato</label>
                <p>{{ $ajusteContrato->contrato->nombre }}</p>
            </div>

            <div class="form-group">
                <label for="contrato_firmado">Contrato Firmado</label>
                <p>{{ $ajusteContrato->contrato_firmado ? 'Sí' : 'No' }}</p>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <p>{{ $ajusteContrato->estado == 'activo' ? 'Activo' : 'Inactivo' }}</p>
            </div>

            <div class="form-group">
                <a href="{{ route('ajustes_contrato.index') }}" class="btn btn-default">Regresar</a>
            </div>
        </div>
    </div>
@endsection
