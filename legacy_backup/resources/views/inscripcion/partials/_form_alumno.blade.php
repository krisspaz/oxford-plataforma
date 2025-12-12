@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<h1>Datos del Estudiante</h1>

<div class="row mt-3">
    <!-- Columna para la fotografía (izquierda) -->
    <div class="col-md-3">
        <div class="form-group">
            <label for="fotografia_estudiante">Fotografía:</label>
            <input 
                type="file" 
                class="form-control-file" 
                id="fotografia_estudiante" 
                name="fotografia_estudiante" 
                accept="image/*" 
                value="{{ old('fotografia_estudiante') }}"
                onchange="previewImage(event)">
            
            <img 
                id="imagePreview" 
                src="{{ isset($persona) && $persona->fotografia ? asset('uploads/fotografias/' . $persona->fotografia) : '#' }}" 
                alt="Vista Previa" 
                style="{{ isset($persona) && $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
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
                            {{ old('privilegio_id_padre', $privilegio->name) == 'ESTUDIANTE' ? 'selected' : '' }}
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
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nombres_estudiante" 
                        name="nombres_estudiante" 
                        value="{{ old('nombres_estudiante') }}"
                        oninput="generateEmail()" 
                        required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="apellidos_estudiante">Apellidos</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="apellidos_estudiante" 
                        name="apellidos_estudiante" 
                        value="{{ old('apellidos_estudiante') }}"
                        oninput="generateEmail()" 
                        required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="genero_estudiante">Género</label>
                    <select name="genero_estudiante" id="genero_estudiante" class="form-control">
                        <option value="Masculino" {{ old('genero_estudiante') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ old('genero_estudiante') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ old('genero_estudiante') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_nacimiento_estudiante">Fecha de Nacimiento</label>
                    <input 
                        type="date" 
                        id="fecha_nacimiento_estudiante" 
                        name="fecha_nacimiento_estudiante" 
                        class="form-control" 
                        value="{{ old('fecha_nacimiento_estudiante') }}"
                        required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="anios_estudiante">Años Cumplidos</label>
                    <input 
                        type="text" 
                        id="anios_estudiante" 
                        name="anios_estudiante" 
                        class="form-control" 
                        value="{{ old('anios_estudiante') }}" 
                        readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_tipoidentificacion_estudiante">Tipo de Identificación</label>
                    <select name="id_tipoidentificacion_estudiante" id="id_tipoidentificacion_estudiante" class="form-control">
                        @foreach ($tiposIdentificacion as $tipo)
                            <option value="{{ $tipo->id }}" 
                                    {{ old('id_tipoidentificacion_estudiante') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="identificacion_estudiante">No. de Identificación</label>
                    <input 
                        type="text" 
                        name="identificacion_estudiante" 
                        class="form-control" 
                        value="{{ old('identificacion_estudiante') }}"
                        required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="carnet_estudiante">Código/Carnet</label>
                    <input 
                        type="text" 
                        name="carnet_estudiante" 
                        class="form-control" 
                        value="{{ old('carnet_estudiante') }}"
                        required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono_estudiante">Teléfono</label>
                    <input 
                        type="text" 
                        name="telefono_estudiante" 
                        class="form-control" 
                        value="{{ old('telefono_estudiante') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="correo_estudiante">Email</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="correo_estudiante" 
                        name="correo_estudiante" 
                        placeholder="Correo electrónico" 
                        value="{{ old('correo_estudiante') }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="direccion_estudiante">Dirección</label>
                    <textarea 
                        name="direccion_estudiante" 
                        id="direccion_estudiante" 
                        rows="3" 
                        class="form-control" 
                        placeholder="Escribe la dirección completa" 
                        required>{{ old('direccion_estudiante') }}</textarea>
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

<script>
    function generateEmail() {
        const nombre = document.getElementById('nombres_estudiante').value.trim();
        const apellido = document.getElementById('apellidos_estudiante').value.trim();
        const emailField = document.getElementById('correo_estudiante');

        if (nombre && apellido) {
            const inicialNombre = nombre.split(' ')[0].charAt(0).toLowerCase();
            const primerApellido = apellido.split(' ')[0].toLowerCase();
            const correoGenerado = `${inicialNombre}${primerApellido}@gmail.com`;

            if (!emailField.value) {
                emailField.value = correoGenerado;
            }
        }
    }
</script>
