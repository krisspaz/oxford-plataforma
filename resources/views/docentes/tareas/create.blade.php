@extends('crudbooster::admin_template')


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
    <h1>Crear Tarea</h1>

    <form action="{{ route('docentes.tareas.store') }}" method="POST">
        @csrf

       
        
        <div class="form-group">
            <label for="bimestre_id">Bimestre</label>
            <select name="bimestre_id" id="bimestre_id" class="form-control" style="width: 10%">
                @if($bimestreActual)
                    <option value="{{ $bimestreActual->id }}">
                        {{ $bimestreActual->nombre }}
                    </option>
                @endif
                
            </select>         
    </div>

        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
        </div>

        <div class="form-group">
            <label for="valor">Punteo de la Tarea</label>
            <input type="number" class="form-control" id="punteo" name="punteo" required min="1" step="0.01" placeholder="Ej. 10">
        </div>

        <div class="form-group">
            <label for="materia_id">Materias</label>
            <select class="form-control" id="materia_id" name="materia_id[]" multiple required>
                @foreach($materias as $materia)
                    <option value="{{ $materia->id }}" 
                        {{ (collect(old('materia_id'))->contains($materia->id)) ? 'selected' : '' }}>
                        {{ $materia->gestionMateria->nombre }}: {{ $materia->cgshe->grados->nombre }} {{ $materia->cgshe->cursos->curso }}, 
                        Sección: "{{ $materia->cgshe->secciones->seccion }}", 
                        Jornada: "{{ $materia->cgshe->jornadas->jornada->nombre }}"
                    </option>
                @endforeach
            </select>
            @error('materia_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="docente_id">Docente</label>
            <select class="form-control" id="docente_id" name="docente_id" required>
                <option value="">Seleccione un docente</option>
                @foreach($docentes as $docente)
                    <option value="{{ $docente->id }}">{{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="fexpiracion">Fecha de Entrega</label>
            <input type="date" class="form-control" id="fexpiracion" name="fexpiracion" required>
        </div>

        <div class="form-group">
            <label for="tiempo_extra_automatico">Tiempo Extra Automático</label>
            <select class="form-control" id="tiempo_extra_automatico" name="tiempo_extra_automatico" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select class="form-control" id="estado_id" name="estado_id" required>
                <option value="">Seleccione un estado</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Crear</button>
        <a href="{{ route('docentes.tareas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection