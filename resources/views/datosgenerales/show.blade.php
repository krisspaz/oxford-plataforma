@extends('crudbooster::admin_template')

@section('title', 'Datos Generales')

@section('content')
    <div class="container">
        <h1>Detalles de la Familia</h1>
        <p><strong>Código Familiar:</strong> {{ $familia->codigofamiliar }}</p>
        
        <h3>Padre</h3>
        <p><strong>Nombre:</strong> {{ $familia->padre->nombre }} {{ $familia->padre->apellido }}</p>
        <!-- Mostrar más información del padre -->
        
        <h3>Madre</h3>
        <p><strong>Nombre:</strong> {{ $familia->madre->nombre }} {{ $familia->madre->apellido }}</p>
        <!-- Mostrar más información de la madre -->

        <h3>Encargado</h3>
        <p><strong>Nombre:</strong> {{ $familia->encargado->nombre }} {{ $familia->encargado->apellido }}</p>
        <!-- Mostrar más información del encargado -->

        <a href="{{ route('datosgenerales.index') }}" class="btn btn-secondary">Volver</a>
    </div>
@endsection
