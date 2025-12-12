@extends('crudbooster::admin_template')



@section('content')
    <h1>Editar Grado-Carrera</h1>

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

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('grado-carreras.update', $gradoCarrera->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="grado_id">Grado:</label>
            <select name="grado_id" id="grado_id" required>
                @foreach ($grados as $grado)
                    <option value="{{ $grado->id }}" {{ $grado->id == $gradoCarrera->grado_id ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="carrera_id">Carrera:</label>
            <select name="carrera_id" id="carrera_id" required>
                @foreach ($carreras as $carrera)
                    <option value="{{ $carrera->id }}" {{ $carrera->id == $gradoCarrera->carrera_id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado_id">Estado:</label>
            <select name="estado_id" id="estado_id" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == $gradoCarrera->estado_id ? 'selected' : '' }}>{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection
