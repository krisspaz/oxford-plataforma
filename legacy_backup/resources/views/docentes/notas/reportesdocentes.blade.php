@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Tareas Calificadas 2</h3>
        </div>

        <form method="POST" action="{{ route('cuadro_notas.generateReport') }}">
            @csrf
            <div class="box-body">

                <input type="hidden" name="docente_id" value="{{ $docentes }}">







                <div class="form-group">
                    <label for="materia">Materia</label>
                    <select name="materia_id" id="materia_id" class="form-control" required>
                        <option value="">Seleccione una Materia</option>

                        @foreach ($materias as $materia)

                                <option value="{{$materia->id }}">
                                  {{$materia->gestionMateria->nombre }} {{"("}} {{$materia->cgshe->grados->nombre }}{{")"}} <!-- Muestra el nombre de la materia -->
                                </option>

                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="bimestre_id">Bimestre</label>
                    <select name="bimestre_id" id="bimestre_id" class="form-control" >
                        <option value="">Todos</option>
                        @foreach ($bimestres->unique('nombre') as $bimestre)
                            <option value="{{ $bimestre->id }}">{{ $bimestre->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="ciclo_escolar">Ciclo Escolar</label>
                    <select name="ciclo_escolar_id" id="ciclo_escolar" class="form-control" required>
                        <option value="">Seleccione un Ciclo Escolar</option>
                        @foreach ($ciclos as $ciclo)
                            <option value="{{ $ciclo->ciclo_escolar }}">{{ $ciclo->ciclo_escolar }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-print"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>
@endsection
