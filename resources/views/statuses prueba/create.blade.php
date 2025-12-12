@extends('crudbooster::admin_template')

@section('title', 'Create Status')

@section('content')
    <h1>Create Status</h1>
    <form action="{{ route('statuses.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="status_name">Status Name:</label>
            <input type="text" id="status_name" name="status_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <a href="{{ route('statuses.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
