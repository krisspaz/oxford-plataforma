@extends('crudbooster::admin_template')

@section('title', 'Show Status')

@section('content')
    <h1>Status Details</h1>
    <p><strong>ID:</strong> {{ $status->id }}</p>
    <p><strong>Status Name:</strong> {{ $status->status_name }}</p>
    <a href="{{ route('statuses.index') }}" class="btn btn-secondary">Back to List</a>
@endsection

