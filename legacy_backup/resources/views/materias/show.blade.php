@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Detalles de la Materia</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>Materia:</th>
                    <td>{{ $materia->gestionMateria->nombre }}</td>
                </tr>
                <tr>
                    <th>Gestión Escolar:</th>
                    <td>{{ $materia->cgshe->gestiones->gestion ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Nivel:</th>
                    <td>{{ $materia->cgshe->niveles->nivel ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Curso:</th>
                    <td>{{ $materia->cgshe->cursos->curso ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Grado:</th>
                    <td>{{ $materia->cgshe->grados->nombre ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Sección:</th>
                    <td>{{ $materia->cgshe->secciones->seccion ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Jornada:</th>
                    <td>{{ $materia->cgshe->jornadas->jornada->nombre ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td>{{ $materia->estado->estado ?? 'No asignado' }}</td>
                </tr>
            </table>
        </div>
        <div class="box-footer">
            <a href="{{ route('materias.index') }}" class="btn btn-default">Volver</a>
            <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-primary">Editar</a>
        </div>
    </div>
@endsection
