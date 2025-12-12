<!-- resources/views/ajustes_asignacion/edit.blade.php -->
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
<div class="container">
    <h2>Editar Estudiante</h2>

    <form action="{{ route('ajustes_asignacion.update', $estudiante->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="persona_id">Persona</label>
            <select name="persona_id" id="persona_id" class="form-control" required>
                @foreach ($personas as $persona)
                    <option value="{{ $persona->id }}" {{ $estudiante->persona_id == $persona->id ? 'selected' : '' }}>
                        {{ $persona->nombres }} {{ $persona->apellidos }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="fotografia_estudiante">Fotografía</label>
            <input type="file" name="fotografia_estudiante" id="fotografia_estudiante" class="form-control">
            @if ($estudiante->fotografia_estudiante)
                <img src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}" alt="Foto Estudiante" width="100">
            @endif
        </div>

        <div class="form-group">
            <label for="nivel_id">Nivel</label>
            <select name="nivel_id" id="nivel_id" class="form-control" required>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ $estudiante->nivel_id == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="cgshges_id">CGSHGE</label>
            <select name="cgshges_id" id="cgshges_id" class="form-control" required>
                @foreach ($cgshges as $cgshge)
                    <option value="{{ $cgshge->id }}" {{ $estudiante->cgshges_id == $cgshge->id ? 'selected' : '' }}>
                        {{ $cgshge->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select name="estado_id" id="estado_id" class="form-control" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estudiante->estado_id == $estado->id ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-warning">Actualizar Estudiante</button>
    </form>
</div>
@endsection
