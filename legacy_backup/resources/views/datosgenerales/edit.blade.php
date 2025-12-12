@extends('crudbooster::admin_template')

@section('title', 'Datos Generales')

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
        <h1>Editar Familia</h1>
        <form action="{{ route('datosgenerales.update', $familia->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <h3>Información del Padre</h3>
                <label for="padre_nombre">Nombre</label>
                <input type="text" name="padre[nombre]" class="form-control" value="{{ $familia->padre->nombre }}" required>
                
                <label for="padre_apellido">Apellido</label>
                <input type="text" name="padre[apellido]" class="form-control" value="{{ $familia->padre->apellido }}" required>

                <label for="padre_identificacion_documentos_id">Documento de Identificación</label>
                <select name="padre[identificacion_documentos_id]" class="form-control" required>
                    @foreach($documentos as $documento)
                        <option value="{{ $documento->id }}" {{ $familia->padre->identificacion_documentos_id == $documento->id ? 'selected' : '' }}>
                            {{ $documento->nombre }}
                        </option>
                    @endforeach
                </select>

                <!-- Añadir más campos del padre según sea necesario -->
            </div>

            <div class="form-group">
                <h3>Información de la Madre</h3>
                <!-- Campos similares para la madre -->
            </div>

            <div class="form-group">
                <h3>Información del Encargado</h3>
                <!-- Campos similares para el encargado -->
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Familia</button>
        </form>
    </div>
@endsection
