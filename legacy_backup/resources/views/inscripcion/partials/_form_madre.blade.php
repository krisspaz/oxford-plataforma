<h1>Datos de la Madre</h1>

<div class="row mt-3">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="privilegio_id_madre">Nombre del Privilegio</label>
                    <select name="privilegio_id_madre" id="privilegio_id_madre" class="form-control">
                        @foreach ($privilegios as $privilegio)
                            <option value="{{ $privilegio->id }}" 
                                {{ old('privilegio_id_madre', $privilegio->name == 'PADRES' ? $privilegio->id : '') == $privilegio->id ? 'selected' : '' }}
                                {{ $privilegio->name != 'PADRES' ? 'disabled' : '' }}>
                                {{ $privilegio->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nombres_madre">Nombres</label>
                    <input type="text" name="nombres_madre" class="form-control" value="{{ old('nombres_madre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="apellidos_madre">Apellidos</label>
                    <input type="text" name="apellidos_madre" class="form-control" value="{{ old('apellidos_madre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="genero_madre">Género</label>
                    <select name="genero_madre" id="genero_madre" class="form-control">
                        <option value="Femenino" {{ old('genero_madre') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="estadocivil_madre">Estado Civil</label>
                    <select name="estadocivil_madre" id="estadocivil_madre" class="form-control">
                        <option value="Soltero" {{ old('estadocivil_madre') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                        <option value="Casado" {{ old('estadocivil_madre') == 'Casado' ? 'selected' : '' }}>Casado</option>
                        <option value="Unido" {{ old('estadocivil_madre') == 'Unido' ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_tipo_madre">Tipo de Identificación</label>
                    <select name="id_tipo_madre" id="id_tipo_madre" class="form-control">
                        @foreach ($tiposIdentificacion as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('id_tipo_madre') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="identificacion_madre">No. de Identificación</label>
                    <input type="text" name="identificacion_madre" class="form-control" value="{{ old('identificacion_madre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_nacimiento_madre">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento_madre" id="fecha_nacimiento_madre" class="form-control" value="{{ old('fecha_nacimiento_madre') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="anios_madre">Años Cumplidos</label>
                    <input type="text" name="anios_madre" class="form-control" readonly value="{{ old('anios_madre') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="defuncion_madre">QEPD</label>
                    <input type="date" name="defuncion_madre" id="defuncion_madre" class="form-control" value="{{ old('defuncion_madre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="profesion_madre">Profesión u Oficio</label>
                    <input type="text" name="profesion_madre" class="form-control" value="{{ old('profesion_madre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="telefono_madre">Teléfono</label>
                    <input type="text" name="telefono_madre" class="form-control" value="{{ old('telefono_madre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="correo_madre">Email</label>
                    <input type="email" name="correo_madre" class="form-control" value="{{ old('correo_madre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion_madre">Dirección</label>
                    <textarea name="direccion_madre" id="direccion_madre" rows="3" class="form-control" placeholder="Escribe la dirección completa">{{ old('direccion_madre') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input type="hidden" name="unico_madre" value="0">
                    <input class="form-check-input" type="checkbox" id="unico_madre" name="unico_madre" value="1" {{ old('unico_madre') ? 'checked' : '' }}>
                    <label class="form-check-label" for="unico_madre">Familia Monoparental (Solo Madre)</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input type="hidden" name="datos_madre" value="0">
                    <input class="form-check-input" type="checkbox" id="datos_madre" name="datos_madre" value="1"  {{ old('datos_madre') ? 'checked' : '' }}>
                    <label class="form-check-label" for="datos_madre">Asignar como Encargado</label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('fecha_nacimiento_madre').addEventListener('change', function() {
    var fechaNacimiento = new Date(this.value);
    var hoy = new Date();
    var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    var mes = hoy.getMonth() - fechaNacimiento.getMonth();
    var dia = hoy.getDate() - fechaNacimiento.getDate();

    if (mes < 0 || (mes === 0 && dia < 0)) {
        edad--;
    }
    document.getElementsByName('anios_madre')[0].value = edad < 0 ? 0 : edad;
});
</script>
