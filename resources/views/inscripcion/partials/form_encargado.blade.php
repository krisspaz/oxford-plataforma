<h1>Datos del Encargado</h1>

<div class="row mt-3">
    <!-- Div General-->
    <div id="ocultar">
        <div class="col-md-9">
            <!-- Fila para Nombre del Rol -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="privilegio_id_encargado">Nombre del Privilegio</label>
                        <select name="privilegio_id_encargado" id="privilegio_id_encargado" class="form-control">
                            @foreach ($privilegios as $privilegio)
                                <option value="{{ $privilegio->id }}" 
                                    {{ old('privilegio_id_encargado', $privilegio->name) == 'ENCARGADO' ? 'selected' : '' }}
                                    {{ $privilegio->name != 'ENCARGADO' ? 'disabled' : '' }}>
                                    {{ $privilegio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fila para Nombre, Apellidos y Género -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nombres_encargado">Nombres</label>
                        <input type="text" name="nombres_encargado" class="form-control" value="{{ old('nombres_encargado') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="apellidos_encargado">Apellidos</label>
                        <input type="text" name="apellidos_encargado" class="form-control" value="{{ old('apellidos_encargado') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="genero_encargado">Género</label>
                        <select name="genero_encargado" id="genero_encargado" class="form-control">
                            <option value="Femenino" {{ old('genero_encargado') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Masculino" {{ old('genero_encargado') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="estadocivil_encargado">Estado Civil</label>
                        <select name="estadocivil_encargado" id="estadocivil_encargado" class="form-control">
                            <option value="Soltero" {{ old('estadocivil_encargado') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                            <option value="Casado" {{ old('estadocivil_encargado') == 'Casado' ? 'selected' : '' }}>Casado</option>
                            <option value="Unido" {{ old('estadocivil_encargado') == 'Unido' ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_tipo_encargado">Tipo de Identificación</label>
                        <select name="id_tipo_encargado" id="id_tipo_encargado" class="form-control">
                            @foreach ($tiposIdentificacion as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('id_tipo_encargado') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="identificacion_encargado">No. de Identificación</label>
                        <input type="text" name="identificacion_encargado" class="form-control" value="{{ old('identificacion_encargado') }}">
                    </div>
                </div>
            </div>

            <!-- Fila para Fecha de Nacimiento, Años Cumplidos y Defunción -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_nacimiento_encargado">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento_encargado" id="fecha_nacimiento_encargado" class="form-control" value="{{ old('fecha_nacimiento_encargado') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="anios_encargado">Años Cumplidos</label>
                        <input type="text" name="anios_encargado" class="form-control" readonly value="{{ old('anios_encargado') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="defuncion_encargado">QEPD</label>
                        <input type="date" name="defuncion_encargado" id="defuncion_encargado" class="form-control" value="{{ old('defuncion_encargado') }}">
                    </div>
                </div>
            </div>

            <!-- Fila para Profesión, Teléfono y Email -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="profesion_encargado">Profesión u Oficio</label>
                        <input type="text" name="profesion_encargado" class="form-control" value="{{ old('profesion_encargado') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefono_encargado">Teléfono</label>
                        <input type="text" name="telefono_encargado" class="form-control" value="{{ old('telefono_encargado') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="correo_encargado">Email</label>
                        <input type="email" name="correo_encargado" class="form-control" value="{{ old('correo_encargado') }}">
                    </div>
                </div>
            </div>

            <!-- Fila para Dirección -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="direccion_encargado">Dirección</label>
                        <textarea name="direccion_encargado" id="direccion_encargado" rows="3" class="form-control" placeholder="Escribe la dirección completa">{{ old('direccion_encargado') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para calcular la edad automáticamente -->
<script>
document.getElementById('fecha_nacimiento_encargado').addEventListener('change', function() {
    var fechaNacimiento = new Date(this.value);
    var hoy = new Date();
    var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    var mes = hoy.getMonth() - fechaNacimiento.getMonth();
    var dia = hoy.getDate() - fechaNacimiento.getDate();

    if (mes < 0 || (mes === 0 && dia < 0)) {
        edad--;
    }
    document.getElementsByName('anios_encargado')[0].value = edad < 0 ? 0 : edad;
});
</script>
