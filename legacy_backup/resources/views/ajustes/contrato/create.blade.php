@extends('crudbooster::admin_template')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Nuevo Contrato Estudiantil</h3>
        </div>

        <div class="panel-body">
            <form action="{{ route('ajustes_contrato.store') }}" method="POST">
                @csrf

                <!-- Selección de Estudiante -->
                <div class="form-group">
                    <label for="estudiante_id">Estudiante</label>
                    <select name="estudiante_id" id="estudiante_id" class="form-control" required>
                        <option value="">Seleccione un estudiante</option>
                        @foreach ($estudiantes as $estudiante)
                            <option value="{{ $estudiante->id }}">{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Selección de Contrato -->
                <div class="form-group">
                    <label for="contrato_id">Contrato</label>
                    <select name="contrato_id" id="contrato_id" class="form-control" required>
                        <option value="">Seleccione un contrato</option>
                        @foreach ($contratos as $contrato)
                            <option value="{{ $contrato->id }}">{{ $contrato->nombre }}</option>
                        @endforeach
                    </select>
                </div>

             

                <!-- Estado del Contrato -->
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control" required>
                        <option value="">Seleccione el estado</option>
                        <option value="Vigente">Vigente</option>
                        <option value="Vencido">Vencido</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Guardar Contrato</button>
                    <a href="{{ route('ajustes_contrato.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
