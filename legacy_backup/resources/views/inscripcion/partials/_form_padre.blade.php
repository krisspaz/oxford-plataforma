<h1>Datos del Padre</h1>

<div class="row mt-3">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="privilegio_id_padre">Nombre del Privilegio</label>
                    <select name="privilegio_id_padre" id="privilegio_id_padre" class="form-control">
                        @foreach ($privilegios as $privilegio)
                            <option value="{{ $privilegio->id }}" 
                                {{ old('privilegio_id_padre', $privilegio->name) == 'PADRES' ? 'selected' : '' }}
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
                    <label for="nombres_padre">Nombres</label>
                    <input type="text" name="nombres_padre" class="form-control" value="{{ old('nombres_padre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="apellidos_padre">Apellidos</label>
                    <input type="text" name="apellidos_padre" class="form-control" value="{{ old('apellidos_padre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="genero_padre">Género</label>
                    <select name="genero_padre" id="genero_padre" class="form-control">
                        <option value="Masculino" {{ old('genero_padre') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="estadocivil_padre">Estado Civil</label>
                    <select name="estadocivil_padre" id="estadocivil_padre" class="form-control">
                        <option value="Soltero" {{ old('estadocivil_padre') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                        <option value="Casado" {{ old('estadocivil_padre') == 'Casado' ? 'selected' : '' }}>Casado</option>
                        <option value="Unido" {{ old('estadocivil_padre') == 'Unido' ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_tipo_padre">Tipo de Identificación</label>
                    <select name="id_tipo_padre" id="id_tipo_padre" class="form-control">
                        @foreach ($tiposIdentificacion as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('id_tipo_padre') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="identificacion_padre">No. de Identificación</label>
                    <input type="text" name="identificacion_padre" class="form-control" value="{{ old('identificacion_padre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_nacimiento_padre">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento_padre" id="fecha_nacimiento_padre" class="form-control" value="{{ old('fecha_nacimiento_padre') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="anios_padre">Años Cumplidos</label>
                    <input type="text" name="anios_padre" class="form-control" value="{{ old('anios_padre') }}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="defuncion_padre">QEPD</label>
                    <input type="date" name="defuncion_padre" id="defuncion_padre" class="form-control" value="{{ old('defuncion_padre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="profesion_padre">Profesión u Oficio</label>
                    <input type="text" name="profesion_padre" class="form-control" value="{{ old('profesion_padre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="telefono_padre">Teléfono</label>
                    <input type="text" name="telefono_padre" class="form-control" value="{{ old('telefono_padre') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="correo_padre">Email</label>
                    <input type="email" name="correo_padre" class="form-control" value="{{ old('correo_padre') }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion_padre">Dirección</label>
                    <textarea name="direccion_padre" id="direccion_padre" rows="3" class="form-control" placeholder="Escribe la dirección completa">{{ old('direccion_padre') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input type="hidden" name="unico_padre" value="0" >
                    <input class="form-check-input" type="checkbox" id="unico_padre" name="unico_padre" value="1"  {{ old('unico_padre') ? 'checked' : '' }}>
                    <label class="form-check-label" for="unico_padre">Familia Monoparental (Solo Padre)</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                
                    <input class="form-check-input" type="checkbox" id="datos_padre" name="datos_padre" value="1"  {{ old('datos_padre') ? 'checked' : '' }}>
                    <label class="form-check-label" for="datos_padre">Asignar como Encargado</label>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('fecha_nacimiento_padre').addEventListener('change', function() {
    var fechaNacimiento = new Date(this.value);
    var hoy = new Date();
    var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    var mes = hoy.getMonth() - fechaNacimiento.getMonth();
    var dia = hoy.getDate() - fechaNacimiento.getDate();

    if (mes < 0 || (mes === 0 && dia < 0)) {
        edad--;
    }
    document.getElementsByName('anios_padre')[0].value = edad < 0 ? 0 : edad;
});
</script>
