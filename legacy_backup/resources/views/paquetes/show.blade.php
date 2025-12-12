@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <h1>Detalles del Paquete</h1>
    <div class="card">
        <div class="card-header">
            <h2>{{ $paquete->nombre }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Descripción:</strong> {{ $paquete->descripcion }}</p>
            <p><strong>Precio:</strong> Q.{{ number_format($paquete->precio, 2) }}</p>
            <p><strong>Estado:</strong> {{ $paquete->estado->estado ?? 'No asignado' }}</p>

            <h4>Cursos Asociados</h4>
            @if ($paquete->cursos->isEmpty())
                <p>No hay cursos asociados a este paquete.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre del Curso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paquete->cursos as $index => $curso)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $curso->curso }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('paquetes.index') }}" class="btn btn-secondary">Volver</a>
            <a href="{{ route('paquetes.edit', $paquete->id) }}" class="btn btn-primary">Editar</a>
        </div>
    </div>
</div>
@endsection
