@extends('crudbooster::admin_template')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Detalles de la Nota Final</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('cuadro-notas.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label">Estudiante:</label>
                <div class="col-sm-9">
                    <p class="form-control-static">{{ $nota->estudiante->persona->nombres ?? '-' }} {{ $nota->estudiante->persona->apellidos ?? '-' }}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Materia:</label>
                <div class="col-sm-9">
                    <p class="form-control-static">{{ $nota->materia->gestionMateria->nombre ?? '-' }}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Bimestre:</label>
                <div class="col-sm-9">
                    <p class="form-control-static">{{ $nota->bimestres->nombre }}</p>
                </div>
            </div>

          

            <div class="form-group">
                <label class="col-sm-3 control-label">Nota Bimestral:</label>
                <div class="col-sm-9">
                    <p class="form-control-static">{{ $nota->nota_acumulada }}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Ciclo Escolar:</label>
                <div class="col-sm-9">
                    <p class="form-control-static">{{ $nota->ciclo_escolar }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="box-footer">
        <a href="{{ route('cuadro-notas.edit', $nota->id) }}" class="btn btn-warning">
            <i class="fa fa-pencil"></i> Editar
        </a>
        <form action="{{ route('cuadro-notas.destroy', $nota->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta nota?')">
                <i class="fa fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@endsection
