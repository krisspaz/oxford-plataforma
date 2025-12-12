<div class="form-group">
    <label for="parentesco_id">Parentesco</label>
    <select name="parentesco_id" id="parentesco_id" class="form-control">
        @foreach ($parentescos as $parentesco)
            <option value="{{ $parentesco->id }}" {{ isset($encargado) && $encargado->parentesco_id == $parentesco->id ? 'selected' : '' }}>
                {{ $parentesco->parentesco }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $encargado->nombre ?? '') }}">
</div>
<div class="form-group">
    <label for="apellido">Apellido</label>
    <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $encargado->apellido ?? '') }}">
</div>
<div class="form-group">
    <label for="identificacion_documentos_id">Tipo de Documento</label>
    <select name="identificacion_documentos_id" id="identificacion_documentos_id" class="form-control">
        @foreach ($identificacionDocumentos as $documento)
            <option value="{{ $documento->id }}" {{ isset($encargado) && $encargado->identificacion_documentos_id == $documento->id ? 'selected' : '' }}>
                {{ $documento->nombre }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="num_documento">Número de Documento</label>
    <input type="text" name="num_documento" id="num_documento" class="form-control" value="{{ old('num_documento', $encargado->num_documento ?? '') }}">
</div>
<div class="form-group">
    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $encargado->fecha_nacimiento ?? '') }}">
</div>
<div class="form-group">
    <label for="profesion">Profesión</label>
    <input type="text" name="profesion" id="profesion" class="form-control" value="{{ old('profesion', $encargado->profesion ?? '') }}">
</div>
<div class="form-group">
    <label for="telefono">Teléfono</label>
    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $encargado->telefono ?? '') }}">
</div>
<div class="form-group">
    <label for="departamento_id">Departamento</label>
    <select name="departamento_id" id="departamento_id" class="form-control">
        <option value="">Seleccione un departamento</option>
        @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->id }}" {{ isset($encargado) && $encargado->municipio->departamento_id == $departamento->id ? 'selected' : '' }}>
                {{ $departamento->departamento }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="municipio_id">Municipio</label>
    <select name="municipio_id" id="municipio_id" class="form-control">
        @if(isset($municipios))
            @foreach ($municipios as $municipio)
                <option value="{{ $municipio->id }}" {{ isset($encargado) && $encargado->municipio_id == $municipio->id ? 'selected' : '' }}>
                    {{ $municipio->municipio }}
                </option>
            @endforeach
        @endif
    </select>
</div>
<div class="form-group">
    <label for="direccion">Dirección</label>
    <textarea name="direccion" id="direccion" class="form-control">{{ old('direccion', $encargado->direccion ?? '') }}</textarea>
</div>

