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
        <h1>Editar Nivel Sucursal</h1>
        <form action="{{ route('niveles_sucursals.update', $niveles_sucursal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="sucursal_id">Sucursal:</label>
                <select name="sucursal_id" id="sucursal_id" class="form-control">
                    @foreach($sucursals as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ $niveles_sucursal->sucursal_id == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre_sucursal }}
                        </option>
                    @endforeach
                </select>
                @error('sucursal_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nivel_id">Nivel:</label>
                <select name="nivel_id" id="nivel_id" class="form-control">
                    @foreach($niveles as $nivel)
                        <option value="{{ $nivel->id }}" {{ $niveles_sucursal->nivel_id == $nivel->id ? 'selected' : '' }}>
                            {{ $nivel->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('nivel_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="estado_id">Estado:</label>
                <select name="estado_id" id="estado_id" class="form-control">
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ $niveles_sucursal->estado_id == $estado->id ? 'selected' : '' }}>
                            {{ $estado->estado }}
                        </option>
                    @endforeach
                </select>
                @error('estado_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('niveles_sucursals.index') }}" class="btn btn-secondary">Volver al listado</a>
        </form>
    </div>
@endsection
