@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Generar Reporte de Boletas</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('reporte.boletas.filtro') }}" method="GET">
            @csrf

            <div class="form-group">
                <label for="grado_id">Grado</label>
                <select class="form-control" name="grado_id" id="grado_id" required style="width: 25%">
                    <option value="">Seleccionar Grado</option>
                    @foreach($grados as $grado)
                        <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="curso_id">Curso</label>
                <select class="form-control" name="curso_id" id="curso_id" required style="width: 25%">
                    <option value="">Seleccionar Curso</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}">{{ $curso->curso }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jornada_id">Jornada</label>
                <select class="form-control" name="jornada_id" id="jornada_id" required style="width: 25%">
                    <option value="">Seleccionar Jornada</option>
                    @foreach($jornadas as $jornada)
                        <option value="{{ $jornada->id }}">{{ $jornada->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="gestion_id">Ciclo Escolar</label>
                <select class="form-control" name="gestion_id" id="gestion_id" required style="width: 25%">
                    <option value="">Seleccionar Ciclo Escolar</option>
                    @foreach($gestiones as $gestion)
                    <option value="{{ $gestion->id }}">{{ $gestion->ciclo_escolar }}</option>
                @endforeach
                </select>
            </div>

             <div class="form-group">
                <label for="promedio">Tipo de Promedio</label>
                <select name="promedio" class="form-control" required style="width: 30%">
                    <option value="">Seleccione el Tipo de Promedio </option>

                        <option value="puntuacion">{{ "Nota Bimestral" }}</option>
                         <option value="porcentaje">{{ "Porcentaje Bimestral" }}</option>
                          <option value="mixto">{{ "Nota Bimestral con Promedio Porcentual" }}</option>

                </select>
            </div>

            <br>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
        </form>
    </div>
</div>
@endsection
