<div class="mb-3">
    <label for="estudiante_id" class="form-label">Estudiante</label>
    <select name="estudiante_id" id="estudiante_id" class="form-select" required>
        <option value="">Seleccione un estudiante</option>
        @foreach($estudiantes as $estudiante)
            <option value="{{ $estudiante->id }}"
                {{ isset($matriculacion) && $matriculacion->estudiante_id == $estudiante->id ? 'selected' : '' }}>
                {{ $estudiante->persona->nombres }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="paquete_id" class="form-label">Paquete</label>
    <select name="paquete_id" id="paquete_id" class="form-select" required>
        <option value="">Seleccione un paquete</option>
        @foreach($paquetes as $paquete)
            <option value="{{ $paquete->id }}"
                {{ isset($matriculacion) && $matriculacion->paquete_id == $paquete->id ? 'selected' : '' }}>
                {{ $paquete->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="fecha_inscripcion" class="form-label">Fecha de Inscripción</label>
    <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" class="form-control"
           value="{{ $matriculacion->fecha_inscripcion ?? old('fecha_inscripcion') }}" required>
</div>

<div class="mb-3">
    <label for="estado_id" class="form-label">Estado</label>
    <select name="estado_id" id="estado_id" class="form-select" required>
        <option value="">Seleccione un estado</option>
        @foreach($estados as $estado)
            <option value="{{ $estado->id }}"
                {{ isset($matriculacion) && $matriculacion->estado_id == $estado->id ? 'selected' : '' }}>
                {{ $estado->nombre }}
            </option>
        @endforeach
    </select>
</div>
