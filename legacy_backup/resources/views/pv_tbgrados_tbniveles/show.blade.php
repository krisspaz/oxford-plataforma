@extends('crudbooster::admin_template')


@section('content')
    <h1>Detalles de la Relación</h1>

    <div>
        <strong>ID:</strong>
        <span>{{ $relation->id }}</span>
    </div>
    <div>
        <strong>Grado:</strong>
        <span>{{ $relation->grado->nombre }}</span>
    </div>
    <div>
        <strong>Nivel:</strong>
        <span>{{ $relation->nivel->nombre }}</span>
    </div>
    <div>
        <strong>Estado:</strong>
        <span>{{ $relation->estado->estado }}</span>
    </div>

    <a href="{{ route('pv_tbgrados_tbniveles.index') }}">Volver</a>
    <a href="{{ route('pv_tbgrados_tbniveles.edit', $relation->id) }}">Editar</a>

    <form action="{{ route('pv_tbgrados_tbniveles.destroy', $relation->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar esta relación?')">Eliminar</button>
    </form>
@endsection

