@extends('crudbooster::admin_template')

@section('title', 'Editar Calificación')

@section('content')
    <div class="box">

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
        <div class="box-header">
            <h1 class="box-title">Editar Calificación</h1>
        </div>
        <div class="box-body">
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('calificaciones.update', $calificacion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="calificacion">Calificación:</label>
                <input type="number" name="calificacion" class="form-control" min="0" max="100" value="{{ old('calificacion', $calificacion->calificacion) }}"  step="0.01" required>
            </div>

            <div class="form-group">
                <label for="comentarios">Comentarios:</label>
                <textarea name="comentarios" class="form-control" rows="5">{{ old('comentarios', $calificacion->comentarios) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Calificación</button>
            <a href="{{ route('calificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
