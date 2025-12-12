<div class="form-group">
    <label for="fotografia">Fotografía</label>
    <input type="file" name="fotografia" id="fotografia" class="form-control-file">
    @if(isset($persona->fotografia))
        <img src="{{ asset('storage/' . $persona->fotografia) }}" alt="Fotografía" class="img-thumbnail mt-2" width="150">
    @endif
    <div class="text-danger" id="error-fotografia">{{ $errors->first('fotografia') }}</div>
</div>


<div class="form-group">
    <label for="codigo">Código</label>
    <input type="text" name="codigo" id="codigo" class="form-control" value="{{ old('codigo', $persona->codigo ?? '') }}" required>
    <div class="text-danger" id="error-codigo">{{ $errors->first('codigo') }}</div>
</div>

<div class="form-group">
    <label for="nombres">Nombres</label>
    <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres', $persona->nombres ?? '') }}" required>
    <div class="text-danger" id="error-nombres">{{ $errors->first('nombres') }}</div>
</div>

<div class="form-group">
    <label for="apellidos">Apellidos</label>
    <input type="text" name="apellidos" id="apellidos" class="form-control" value="{{ old('apellidos', $persona->apellidos ?? '') }}" required>
    <div class="text-danger" id="error-apellidos">{{ $errors->first('apellidos') }}</div>
</div>

<div class="form-group">
    <label for="estado_civil">Estado Civil</label>
    <select name="estado_civil" id="estado_civil" class="form-control" required>
        <option value="Solter@" {{ old('estado_civil', $persona->estado_civil ?? '') == 'Solter@' ? 'selected' : '' }}>Solter@</option>
        <option value="Casad@" {{ old('estado_civil', $persona->estado_civil ?? '') == 'Casad@' ? 'selected' : '' }}>Casad@</option>
        <!-- Agrega más opciones si es necesario -->
    </select>
    <div class="text-danger" id="error-estado_civil">{{ $errors->first('estado_civil') }}</div>
</div>


<div class="form-group">
    <label for="apellido_casada">Apellido de Casada</label>
    <input type="text" name="apellido_casada" id="apellido_casada" class="form-control" value="{{ old('apellido_casada', $persona->apellido_casada ?? '') }}">
    <div class="text-danger" id="error-apellido_casada">{{ $errors->first('apellido_casada') }}</div>
</div>

<div class="form-group">
    <label for="genero">Género</label>
    <select name="genero" id="genero" class="form-control" required>
        <option value="Masculino" {{ old('genero', $persona->genero ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
        <option value="Femenino" {{ old('genero', $persona->genero ?? '') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
        <!-- Agrega más opciones si es necesario -->
    </select>
    <div class="text-danger" id="error-genero">{{ $errors->first('genero') }}</div>
</div>





<div class="form-group">
    <label for="nacionalidad">Nacionalidad</label>
    <input type="text" name="nacionalidad" id="nacionalidad" class="form-control" value="{{ old('nacionalidad', $persona->nacionalidad ?? '') }}">
    <div class="text-danger" id="error-nacionalidad">{{ $errors->first('nacionalidad') }}</div>
</div>

<div class="form-group">
    <label for="identificacion_documentos_id">Tipo de Documento</label>
    <select name="identificacion_documentos_id" id="identificacion_documentos_id" class="form-control" required>
        @foreach($identificacionDocumentos as $documento)
            <option value="{{ $documento->id }}" {{ old('identificacion_documentos_id', $persona->identificacion_documentos_id ?? '') == $documento->id ? 'selected' : '' }}>
                {{ $documento->nombre }}
            </option>
        @endforeach
    </select>
    <div class="text-danger" id="error-identificacion_documentos_id">{{ $errors->first('identificacion_documentos_id') }}</div>
</div>

<div class="form-group">
    <label for="num_documento">Número de Documento</label>
    <input type="text" name="num_documento" id="num_documento" class="form-control" value="{{ old('num_documento', $persona->num_documento ?? '') }}" required>
    <div class="text-danger" id="error-num_documento">{{ $errors->first('num_documento') }}</div>
</div>


<div class="form-group">
    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento ? $persona->fecha_nacimiento->format('Y-m-d') : '') }}">
    <div class="text-danger" id="error-fecha_nacimiento">{{ $errors->first('fecha_nacimiento') }}</div>
</div>


<div class="form-group">
    <label for="lugar_nacimiento">Lugar de Nacimiento</label>
    <input type="text" name="lugar_nacimiento" id="lugar_nacimiento" class="form-control" value="{{ old('lugar_nacimiento', $persona->lugar_nacimiento ?? '') }}">
    <div class="text-danger" id="error-lugar_nacimiento">{{ $errors->first('lugar_nacimiento') }}</div>
</div>

<div class="form-group">
    <label for="email">Correo Electrónico</label>
    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $persona->email ?? '') }}">
    <div class="text-danger" id="error-email">{{ $errors->first('email') }}</div>
</div>

<div class="form-group">
    <label for="telefono_casa">Teléfono de Casa</label>
    <input type="text" name="telefono_casa" id="telefono_casa" class="form-control" value="{{ old('telefono_casa', $persona->telefono_casa ?? '') }}">
    <div class="text-danger" id="error-telefono_casa">{{ $errors->first('telefono_casa') }}</div>
</div>

<div class="form-group">
    <label for="telefono_mobil">Teléfono Móvil</label>
    <input type="text" name="telefono_mobil" id="telefono_mobil" class="form-control" value="{{ old('telefono_mobil', $persona->telefono_mobil ?? '') }}">
    <div class="text-danger" id="error-telefono_mobil">{{ $errors->first('telefono_mobil') }}</div>
</div>


<div class="form-group">
    <label for="nit">NIT</label>
    <input type="text" name="nit" id="nit" class="form-control" value="{{ old('nit', $persona->nit ?? '') }}">
    <div class="text-danger" id="error-nit">{{ $errors->first('nit') }}</div>
</div>



<div class="form-group">
    <label for="municipio_id">Municipio</label>
    <select name="municipio_id" id="municipio_id" class="form-control" required>
        @foreach($municipios as $municipio)
            <option value="{{ $municipio->id }}" {{ old('municipio_id', $persona->municipio_id ?? '') == $municipio->id ? 'selected' : '' }}>
                {{ $municipio->municipio }}
            </option>
        @endforeach
    </select>
    <div class="text-danger" id="error-municipio_id">{{ $errors->first('municipio_id') }}</div>
</div>

<div class="form-group">
    <label for="direccion">Dirección</label>
    <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $persona->direccion ?? '') }}">
    <div class="text-danger" id="error-direccion">{{ $errors->first('direccion') }}</div>
</div>

