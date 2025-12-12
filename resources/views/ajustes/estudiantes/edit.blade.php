@extends('crudbooster::admin_template')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Editar Estudiante</h3>
    </div>

    <form method="POST" action="{{ route('ajustes_estudiantes.update', $estudiante->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="box-body">
            <div class="form-group">
                <label for="persona_nombre">Persona</label>
                <input type="text" id="persona_nombre" class="form-control" 
                       value="{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}" readonly>
                <input type="hidden" name="persona_id" value="{{ $estudiante->persona_id }}">
            </div>
            

            <div class="form-group">
                <label for="carnet">Carnet</label>
                <input type="text" name="carnet" id="carnet" class="form-control" value="{{ old('carnet', $estudiante->carnet) }}" required>
            </div>

            <div class="form-group">
                <label for="cgshges_id">Matriculación Reciente</label>
                <select name="cgshges_id" id="cgshges_id" class="form-control" required>
                    <option value="">Seleccione CGSHGE</option>
                    @foreach($cgshges as $cgshge)
                        <option value="{{ $cgshge->id }}" {{ $estudiante->cgshges_id == $cgshge->id ? 'selected' : '' }}>
                            {{ $cgshge->grados->nombre }}   {{ $cgshge->cursos->curso }}{{ "'"}}{{ $cgshge->secciones->seccion }}{{ "'"}} {{ $cgshge->jornadas->jornada->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="estado_id">Estado</label>
                <select name="estado_id" id="estado_id" class="form-control" required>
                    <option value="">Seleccione un estado</option>
                    @foreach($estados as $id => $estado)
                        <option value="{{ $id }}" {{ $estudiante->estado_id == $id ? 'selected' : '' }}>
                            {{ $estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="fotografia_estudiante">Fotografía del Estudiante</label><br>
                @if($estudiante->fotografia_estudiante)
                    <img src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}" alt="Foto estudiante" width="150" height="150" class="img-thumbnail mb-2">
                @endif
                <input type="file" name="fotografia_estudiante" id="fotografia_estudiante" class="form-control">
                <small class="form-text text-muted">Opcional: Actualice la fotografía si desea.</small>
            </div>

        </div>

        <div class="box-footer">
            <a href="{{ route('ajustes_estudiantes.index') }}" class="btn btn-default">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>
</div>
@endsection
