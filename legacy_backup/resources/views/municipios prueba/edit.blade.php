@extends('crudbooster::admin_template')


@section('title', 'Edit Municipio')

@section('content')

@if (session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('error') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif
    <h1>Edit Municipio</h1>
    <form action="{{ route('municipios.update', $municipio) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="municipio">Municipio Name:</label>
            <input type="text" id="municipio" name="municipio" class="form-control" value="{{ $municipio->municipio }}" required>
        </div>
        <div class="form-group">
            <label for="departamento_id">Departamento:</label>
            <select id="departamento_id" name="departamento_id" class="form-control" required>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ $municipio->departamento_id == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->departamento }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="status_id">Status:</label>
            <select id="status_id" name="status_id" class="form-control" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ $municipio->status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->status_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <a href="{{ route('municipios.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
