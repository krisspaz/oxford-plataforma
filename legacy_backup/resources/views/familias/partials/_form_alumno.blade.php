<h1>Datos del Estudiante</h1>

<div class="row mt-3">
    <!-- Columna para la fotografía (izquierda) -->
    <div class="col-md-3">
        <div class="form-group">
            <div class="form-group">
                <label for="fotografia_estudiante">Fotografía:</label>
                <input type="file" class="form-control-file" id="fotografia_estudiante" name="fotografia_estudiante" accept="image/*" onchange="previewImage(event)">
                <img id="imagePreview" 
                     src="{{ isset($persona) && $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" 
                     alt="Vista Previa" 
                     style="{{ isset($persona) && $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>
        </div>
    </div>
    
    <!-- Columna para el resto de los campos (derecha) -->
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="privilegio_id_estudiante">Nombre del Privilegio</label>
                    <select name="privilegio_id_estudiante" id="privilegio_id_estudiante" class="form-control" >
                      
                        @foreach ($privilegios as $privilegio)
                        <option value="{{ $privilegio->id }}" 
                            {{ $privilegio->name == 'ESTUDIANTE' ? 'selected' : '' }}
                             {{ $privilegio->name != 'ESTUDIANTE' ? 'disabled' : '' }}>
                                {{ $privilegio->name }}
                        </option>
                    @endforeach
                      
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nombres_estudiante">Nombres</label>
                    <input type="text" name="nombres_estudiante" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="apellidos_estudiante">Apellidos</label>
                    <input type="text" name="apellidos_estudiante" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="genero_estudiante">Género</label>
                    <select name="genero_estudiante" id="genero_estudiante" class="form-control">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_nacimiento_estudiante">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento_estudiante" name="fecha_nacimiento_estudiante" class="form-control" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="anios_estudiante">Años Cumplidos</label>
                    <input type="text" id="anios_estudiante" name="anios_estudiante" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_tipoidentificacion_estudiante">Tipo de Identificación</label>
                    <select name="id_tipoidentificacion_estudiante" id="id_tipoidentificacion_estudiante" class="form-control">
                        @foreach ($tiposIdentificacion as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="identificacion_estudiante">No. de Identificación</label>
                    <input type="text" name="identificacion_estudiante" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="carnet_estudiante">Código/Carnet</label>
                    <input type="text" name="carnet_estudiante" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono_estudiante">Teléfono</label>
                    <input type="text" name="telefono_estudiante" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="correo_estudiante">Email</label>
                    <input type="email" name="correo_estudiante" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="direccion_estudiante">Dirección</label>
                    <textarea name="direccion_estudiante" id="direccion_estudiante" rows="3" class="form-control" placeholder="Escribe la dirección completa" required></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('fecha_nacimiento_estudiante').addEventListener('change', function() {
    var fechaNacimiento = new Date(this.value);
    var hoy = new Date();
    var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    var mes = hoy.getMonth() - fechaNacimiento.getMonth();
    var dia = hoy.getDate() - fechaNacimiento.getDate();

    if (mes < 0 || (mes === 0 && dia < 0)) {
        edad--;
    }

    if (edad < 0) {
        edad = 0;
    }

    document.getElementById('anios_estudiante').value = edad;
});
</script>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
  

