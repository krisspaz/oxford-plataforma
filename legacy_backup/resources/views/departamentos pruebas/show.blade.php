@extends('crudbooster::admin_template')

@section('title', 'Show Departamento')

@section('content')
    <h1>Departamento Details</h1>
    <p><strong>ID:</strong> {{ $departamento->id }}</p>
    <p><strong>Departamento Name:</strong> {{ $departamento->departamento }}</p>
    <p><strong>Status:</strong> {{ $departamento->status->status_name }}</p>
    <a href="{{ route('departamentos.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
