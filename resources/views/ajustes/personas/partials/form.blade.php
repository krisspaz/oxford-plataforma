<div class="box-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $persona->nombres ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $persona->apellidos ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="genero">Género</label>
                <select name="genero" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Masculino" {{ old('genero', $persona->genero ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('genero', $persona->genero ?? '') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control">
                    <option value="Soltero" {{ $persona->estado_civil == "Soltero" ? 'selected' : '' }}>Soltero</option>
                    <option value="Casado" {{ $persona->estado_civil == "Casado" ? 'selected' : '' }}>Casado</option>
                    <option value="Unido" {{ $persona->estado_civil == "Unido" ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="apellido_casada">Apellido de Casada</label>
                <input type="text" name="apellido_casada" class="form-control" value="{{ old('apellido_casada', $persona->apellido_casada ?? '') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="profesion">Profesión</label>
                <input type="text" name="profesion" class="form-control" value="{{ old('profesion', $persona->profesion ?? '') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion_documentos_id">Tipo de Documento</label>
                <select name="identificacion_documentos_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($documentos as $doc)
                        <option value="{{ $doc->id }}" {{ old('identificacion_documentos_id', $persona->identificacion_documentos_id ?? '') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="num_documento">Número de Documento</label>
                <input type="text" name="num_documento" class="form-control" value="{{ old('num_documento', $persona->num_documento ?? '') }}" required>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input
                type="date"
                name="fecha_nacimiento"
                class="form-control"
                value="{{ old('fecha_nacimiento', isset($persona->fecha_nacimiento) ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('Y-m-d') : '') }}"
            >
        </div>
    </div>
</div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $persona->telefono ?? '') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $persona->email ?? '') }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="direccion">Dirección</label>
        <textarea name="direccion" class="form-control" rows="2">{{ old('direccion', $persona->direccion ?? '') }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fecha_defuncion">Fecha de Defunción</label>
                <input type="date" name="fecha_defuncion" class="form-control" value="{{ old('fecha_defuncion', $persona->fecha_defuncion ?? '') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="parentesco_id">Parentesco</label>
                <select name="parentesco_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    @foreach($parentescos as $p)
                        <option value="{{ $p->id }}" {{ old('parentesco_id', $persona->parentesco_id ?? '') == $p->id ? 'selected' : '' }}>
                            {{ $p->parentesco }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @php
        $noPermitidos = [];
    @endphp



        <div class="col-md-6">
         <!-- El select de privilegios estará oculto por defecto -->
         <div class="form-group" id="privilegio_group" style="display: block;">
            <label for="privilegio_id">Nombre del Privilegio</label>
            <select name="privilegio_id" id="privilegio_id" class="form-control">
                <option value="">-- Seleccione --</option>
                @foreach ($privilegios as $privilegio)
                @if (!in_array($privilegio->name, $noPermitidos))
                <option value="{{ $privilegio->id }}"
                    {{ old('privilegio_id', $privilegio->id) == $persona->cmsUser->cmsPrivilege->id ? 'selected' : '' }}>
                    {{ $privilegio->name }}
                </option>
                @endif
                @endforeach
            </select>
        </div>
    </div>


    </div>
</div>
