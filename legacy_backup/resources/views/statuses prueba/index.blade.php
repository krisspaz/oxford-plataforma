@extends('crudbooster::admin_template')


@section('content')
    <h1>Status List</h1>
    <a href="{{ route('statuses.create') }}" class="btn btn-primary">Create New Status</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Status Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statuses as $status)
                <tr>
                    <td>{{ $status->id }}</td>
                    <td>{{ $status->status_name }}</td>
                    <td>
                        <a href="{{ route('statuses.show', $status) }}" class="btn btn-info">View</a>
                        <a href="{{ route('statuses.edit', $status) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('statuses.destroy', $status) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
