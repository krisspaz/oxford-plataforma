@extends('crudbooster::admin_template')

@section('title', 'Show Municipio')

@section('content')
    <h1>Municipio Details</h1>
    <p><strong>ID:</strong> {{ $municipio->id }}</p>
    <p><strong>Municipio Name:</strong> {{ $municipio->municipio }}</p>
    <p><strong>Departamento:</strong> {{ $municipio->departamento->departamento }}</p>
    <p><strong>Status:</strong> {{ $municipio->status->status_name }}</p>
    <a href="{{ route('municipios.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
