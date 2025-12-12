@extends('crudbooster::admin_template')

@section('title', 'Sucursal Details')


@section('content')
    <h1>Sucursales Details</h1>
    <p><strong>ID:</strong> {{ $sucursale->id }}</p>
    <p><strong>Nombre:</strong> {{ $sucursale->nombre_sucursal }}</p>
    <p><strong>Municipio:</strong> {{ $sucursale->municipio->municipio }}</p>
    <p><strong>Dirección:</strong> {{ $sucursale->direccion }}</p>
    <p><strong>Status:</strong> {{ $sucursale->status->status_name }}</p>
    
    <a href="{{ route('sucursales.index') }}">Volver a la lista</a>
   
   
@endsection
