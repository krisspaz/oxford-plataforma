@extends('crudbooster::admin_template')



@section('content')
    <h1>Editar Relación</h1>

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

    <form action="{{ route('pv_tbgrados_tbniveles.update', $relation->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="grado_id">Grado:</label>
            <select name="grado_id" id="grado_id">
                @foreach ($grados as $grado)
                    <option value="{{ $grado->id }}" {{ $grado->id == $relation->grado_id ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="nivel_id">Nivel:</label>
            <select name="nivel_id" id="nivel_id">
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ $nivel->id == $relation->nivel_id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado_id">Estado:</label>
            <select name="estado_id" id="estado_id">
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == $relation->estado_id ? 'selected' : '' }}>{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection
