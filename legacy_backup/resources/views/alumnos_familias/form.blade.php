<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method == 'PUT')
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="alumno_id">Alumno</label>
        <select name="alumno_id" id="alumno_id" class="form-control" required>
            @foreach($alumnos as $alumno)
                <option value="{{ $alumno->id }}" {{ isset($alumnosFamilia) && $alumnosFamilia->alumno_id == $alumno->id ? 'selected' : '' }}>
                    {{ $alumno->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="padres_tutores_id">Tutor</label>
        <select name="padres_tutores_id" id="padres_tutores_id" class="form-control" required>
            @foreach($padresTutores as $padreTutores)
                <option value="{{ $padreTutores->id }}" {{ isset($alumnosFamilia) && $alumnosFamilia->padres_tutores_id == $padreTutores->id ? 'selected' : '' }}>
                    {{ $padreTutores->encargado->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">{{ $method == 'PUT' ? 'Actualizar' : 'Guardar' }}</button>
</form>
