@extends('crudbooster::admin_template') {{-- Usamos plantilla de CRUD Booster --}}

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Calificación de la Tarea</h3>
        </div>

        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>Estudiante</th>
                        <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                    </tr>
                    <tr>
                        <th>Tarea</th>
                        <td>{{ $tarea->titulo }}</td>
                    </tr>
                    <tr>
                        <th>Calificación</th>
                        <td>{{ $calificacion }}</td>
                    </tr>
                    <tr>
                        <th>Comentario</th>
                        <td>{{ $comentario ?? 'Sin comentario' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
