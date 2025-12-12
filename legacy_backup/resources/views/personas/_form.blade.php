<div class="form-group">
    <label for="{{ $prefix }}[nombres]">Nombres</label>
    <input type="text" class="form-control" name="{{ $prefix }}[nombres]" value="{{ old($prefix . '.nombres') }}" required>
</div>

<div class="form-group">
    <label for="{{ $prefix }}[apellidos]">Apellidos</label>
    <input type="text" class="form-control" name="{{ $prefix }}[apellidos]" value="{{ old($prefix . '.apellidos') }}" required>
</div>

<div class="form-group">
    <label for="{{ $prefix }}[num_documento]">Número de Documento</label>
    <input type="text" class="form-control" name="{{ $prefix }}[num_documento]" value="{{ old($prefix . '.num_documento') }}" required>
</div>

<div class="form-group">
    <label for="{{ $prefix }}[identificacion_documentos_id]">Tipo de Documento</label>
    <select class="form-control" name="{{ $prefix }}[identificacion_documentos_id]" required>
        <option value="">Seleccione</option>
        @foreach ($identificacionDocumentos as $documento)
            <option value="{{ $documento->id }}" {{ old($prefix . '.identificacion_documentos_id') == $documento->id ? 'selected' : '' }}>
                {{ $documento->nombre }}
            </option>
        @endforeach
    </select>
</div>




<div class="form-group">
    <label for="{{ $prefix }}[email]">Correo Electrónico</label>
    <input type="email" class="form-control" name="{{ $prefix }}[email]" value="{{ old($prefix . '.email') }}">
</div>

<div class="form-group">
    <label for="{{ $prefix }}[fotografia]">Fotografía</label>
    <input type="file" class="form-control-file" name="{{ $prefix }}[fotografia]" accept="image/*">
</div>
