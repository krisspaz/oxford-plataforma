@extends('crudbooster::admin_template')




@section('title', 'Edit Status')

@section('content')
    <h1>Edit Status</h1>
    <form action="{{ route('statuses.update', $status) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="status_name">Status Name:</label>
            <input type="text" id="status_name" name="status_name" class="form-control" value="{{ $status->status_name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <a href="{{ route('statuses.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
