@extends('crudbooster::admin_template')


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
    <h1>Editar Departamento</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('departamentos.update', $departamento->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="departamento">Departamento:</label>
            <input type="text" name="departamento" id="departamento" value="{{ $departamento->departamento }}" required>
        </div>
        <div>
            <label for="estado_id">Estado:</label>
            <select name="estado_id" id="estado_id" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == $departamento->estado_id ? 'selected' : '' }}>{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection
