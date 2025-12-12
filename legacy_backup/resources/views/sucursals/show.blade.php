@extends('crudbooster::admin_template')



@section('content')
<div class="container">
    <h1>Detalle de la Sucursal</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><strong>Nombre:</strong>{{ $sucursal->nombre_sucursal }}</h5>
            <p class="card-text"><strong>Municipio:</strong> {{ $sucursal->municipio->municipio }}</p>
            <p class="card-text"><strong>Dirección:</strong> {{ $sucursal->direccion }}</p>
            <p class="card-text"><strong>Estado:</strong> {{ $sucursal->estado->estado }}</p>

           

            <a href="{{ route('sucursals.index') }}" class="btn btn-secondary">Regresar</a>
        </div>
    </div>
</div>
@endsection
