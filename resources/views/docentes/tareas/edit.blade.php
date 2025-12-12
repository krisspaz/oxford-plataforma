
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
    <h1>Editar Tarea</h1>
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <form action="{{ route('docentes.tareas.update', $tarea->id) }}" method="POST">
        @csrf
        @method('PUT')

        

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $tarea->titulo) }}" required style="width: 30%">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="5" style="width: 60%" >{{ old('descripcion', $tarea->descripcion) }}</textarea >
        </div>

        <div class="form-group">
            <label for="valor">Punteo de la Tarea</label>
            <input type="number" class="form-control" id="punteo" name="punteo" required min="1" step="0.01"  value="{{ old('punteo', $tarea->punteo) }}" style="width: 30%">
        </div>



        <div class="mb-3">
            <label for="materia_id" class="form-label">Materia</label>
            <select class="form-select" id="materia_id" name="materia_id" style="width: 30%" >
                @foreach($materias as $materia)
                    <option value="{{ $materia->id }}"
                      {{ old('materia_id', $tarea->materia->id ?? '') == $materia->id ? 'selected' : '' }}>
                        {{ $materia->gestionMateria->nombre }}: {{ $materia->cgshe->grados->nombre }} {{ $materia->cgshe->cursos->curso }},
                        Sección: "{{ $materia->cgshe->secciones->seccion }}",
                        Jornada: "{{ $materia->cgshe->jornadas->jornada->nombre }}"
                    </option>
                @endforeach
            </select>

        </div>



        <div class="mb-3">
            <label for="fexpiracion" class="form-label">Fecha de Expiración</label>
            <input type="date" class="form-control" id="fexpiracion" name="fexpiracion" value="{{ old('fexpiracion', $tarea->fexpiracion) }}" style="width: 30%">
        </div>

        <div class="mb-3">
            <label for="tiempo_extra_automatico" class="form-label">Tiempo Extra Automático</label>
            <select class="form-select" id="tiempo_extra_automatico" name="tiempo_extra_automatico">
                <option value="0" {{ $tarea->tiempo_extra_automatico == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ $tarea->tiempo_extra_automatico == 1 ? 'selected' : '' }}>Sí</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="estado_id" class="form-label">Estado</label>
            <select class="form-select" id="estado_id" name="estado_id" style="width: 30%">
                @foreach($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == $tarea->estado_id ? 'selected' : '' }}>{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
        <a href="{{ route('docentes.tareas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
