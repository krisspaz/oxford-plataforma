@extends('crudbooster::admin_template')

@section('content')
<div class="container">
    <h1>Detalles del Personal Administrativo</h1>
    <a href="{{ route('administrativos.index') }}" class="btn btn-secondary mb-3">Volver</a>

    <div class="card">
        <div class="card-header">
            <p> <img id="imagePreview" 
                src="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? asset('storage/' . $administrativo->fotografia_administrativo) : '#' }}" 
                alt="Vista Previa" 
                style="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
       </div>
            
            </p>
            <h3>{{ $administrativo->persona->nombres }} {{ $administrativo->persona->apellidos }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Cargo:</strong> {{ $administrativo->cargo->nombre }}</p>
          
            <p><strong>Telefono</strong> {{ $administrativo->persona->telefono }}</p>
            <p><strong>Dirección</strong> {{ $administrativo->persona->direccion }}</p>
           
        </div>
    </div>
</div>
@endsection
