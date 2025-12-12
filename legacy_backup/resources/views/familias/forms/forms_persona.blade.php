<h1>Datos del {{ ucfirst($prefijo) }}</h1>
<h1>{{$familiaId}}</h1>
<div class="row mt-6">
    <!-- Columna para la fotografía (izquierda) -->
    <!-- Columna para el resto de los campos (derecha) -->
    <div class="col-md-12">
        <!-- Fila para Nombre del Rol -->
        <div class="row">

        </div>

        <!-- Fila para Nombre, Apellidos y Género -->
        <div class="row">
            <div class="col-md-4">
                <input type="hidden" name="familia_id" value="{{ $familia->id }}">
                <input type="hidden" name="codigofamilia" value="{{ $familia->codigo_familiar }}">
                <input type="hidden" name="prefijo" value="{{$prefijo}}">
                <input type="hidden" name="apellido_pa" value="{{ $familia->padre->apellidos }}">
                <input type="hidden" name="apellido_ma" value="{{ $familia->madre->apellidos }}">
                <div class="form-group">
                    <label for="nombres">Nombres </label>
                    <input type="text" name="nombres" class="form-control" value="{{ $familia->$prefijo->nombres }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ $familia->$prefijo->apellidos }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="genero">Género</label>
                    <select name="genero" id="genero" class="form-control">

                        <option value="{{ $familia->$prefijo->genero }}" {{ $familia->$prefijo->genero== "Masculino" ? 'selected' : '' }}>Masculino</option>
                        <option value="{{ $familia->$prefijo->genero }}" {{ $familia->$prefijo->genero== "Femenino" ? 'selected' : '' }}>Femenino</option>

                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="estadocivil">Estado Civil</label>
                    <select name="estadocivil" id="estadocivil" class="form-control">
                        <option value="Soltero" {{ $familia->$prefijo->estado_civil == "Soltero" ? 'selected' : '' }}>Soltero</option>
                        <option value="Casado" {{ $familia->$prefijo->estado_civil == "Casado" ? 'selected' : '' }}>Casado</option>
                        <option value="Unido" {{ $familia->$prefijo->estado_civil == "Unido" ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_tipo">Tipo de Identificación</label>
                    <select name="id_tipo" id="id_tipo" class="form-control">
                        @if($identificaciones && $identificaciones->isNotEmpty())
                        @foreach ($identificaciones as $tipo)
                            <option value="{{ $tipo->id }}" {{ $tipo->id == $familia->$prefijo->identificacion_documentos_id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    @else
                        <option value="">No hay tipos de identificación disponibles</option>
                    @endif
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label for="identificacion">No. de Identificación</label>
                    <input type="text" name="identificacion" class="form-control" value="{{ $familia->$prefijo->num_documento }}" required>
                </div>
            </div>

        </div>

        <!-- Fila para Fecha de Nacimiento, Años Cumplidos, Tipo de Identificación y No. de Identificación -->
        <div class="row">
            <div class="col-md-5">
            <div class="form-group">
            <label>Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="fecha-nacimiento form-control"
                data-edad-target="anios-{{ $prefijo }}"
                value="{{ $familia->$prefijo->fecha_nacimiento ? $familia->$prefijo->fecha_nacimiento->format('Y-m-d') : '' }}"
                required>
        </div>


            </div>
            <div class="col-md-3">
               <div class="form-group">
                <label>Años Cumplidos</label>
                <input type="text" name="anios" class="anios form-control" id="anios-{{ $prefijo }}" readonly>
            </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="defuncion">QEPD</label>
                    <input type="date" name="defuncion" id="defuncion" class="form-control" value="{{ $familia->$prefijo->fecha_defuncion }}">
                </div>
            </div>

        </div>

        <!-- Fila para Profesión, Teléfono y Email -->
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="profesion">Profesión u Oficio</label>
                    <input type="text" name="profesion" class="form-control" value="{{ $familia->$prefijo->profesion }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono" >Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $familia->$prefijo->telefono }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="correo">Email</label>
                    <input type="email" name="correo" class="form-control" value="{{ $familia->$prefijo->email }}" required>
                </div>
            </div>


        </div>

        <!-- Fila para Dirección -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion">Dirección </label>
                    <textarea name="direccion" id="direccion" rows="3" class="form-control" placeholder="Escribe la dirección completa">{{$familia->$prefijo->direccion}}</textarea>
                </div>
            </div>

            <div class="col-md-4">

            <div class="form-group">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="datos">
                    <label class="form-check-label" for="datos">Asignar como Encargado</label>

                  </div>
             </div>

            </div>
        </div>
    </div>
</div>

<!-- Script para calcular la edad automáticamente -->

<script>
function calcularEdad(fechaInput, edadInput) {
    if (fechaInput.value) {
        const fechaNacimiento = new Date(fechaInput.value);
        const hoy = new Date();

        let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        const mes = hoy.getMonth() - fechaNacimiento.getMonth();
        const dia = hoy.getDate() - fechaNacimiento.getDate();

        if (mes < 0 || (mes === 0 && dia < 0)) {
            edad--;
        }

        edadInput.value = edad < 0 ? 0 : edad;
    } else {
        edadInput.value = '';
    }
}

// Aplicar a todos los inputs de fecha en el modal
document.querySelectorAll('.fecha-nacimiento').forEach(function(input) {
    const targetId = input.dataset.edadTarget;
    const edadInput = document.getElementById(targetId);

    // Calcular al cambiar la fecha
    input.addEventListener('change', function() {
        calcularEdad(input, edadInput);
    });

    // Calcular al cargar la modal
    calcularEdad(input, edadInput);
});
</script>




