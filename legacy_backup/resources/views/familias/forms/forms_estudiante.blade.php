<h1>Datos del {{ ucfirst($prefijo) }}</h1>
<h1>{{$familiaId}}</h1>

<div class="row mt-6">
    <div class="col-md-12">
        <div class="row">
            <!-- Columna para la fotografía -->
            <div class="col-md-4">
                <input type="hidden" name="familia_id" value="{{ $familia->id }}">

                <!-- Imagen de previsualización -->
                <img id="modalfotografia" nama="fotografia" src="" class="img-fluid" style="width: 5cm; height: 4cm;" alt="Foto del estudiante">

                <!-- Botón para cambiar la fotografía -->
                <div class="form-group mt-2">
                    <label for="fotografia">Cambiar Fotografía </label>
                    <input type="file" name="fotografia_estudiante" id="modalFotografiaInput" class="form-control" accept="image/*">
                </div>
            </div>

            <!-- Columna para Nombre, Apellidos y Género -->


            <div class="col-md-4">
                <div class="form-group">
                    <label for="nombres">Nombres </label>
                    <input type="text" name="nombres" id="modalNombre" class="form-control" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="modalApellido" name="apellidos" class="form-control" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="genero">Género</label>
                    <select name="genero" id="modalGenero" class="form-control">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
            </div>

            <!-- Tipo de Identificación y Número de Identificación -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_tipo">Tipo de Identificación</label>
                    <select name="id_tipo" id="modalIdidocumentacion" class="form-control">
                        @if($identificaciones && $identificaciones->isNotEmpty())
                            @foreach ($identificaciones as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
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
                    <input type="text" name="identificacion" id="modalNumdocumento" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Fecha de Nacimiento, Años Cumplidos y QEPD -->
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="modalFnacimientol" class="form-control" required>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="anios">Años Cumplidos</label>
                    <input type="text" name="anios" id="anios" class="form-control" readonly>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="defuncion">QEPD</label>
                    <input type="date" name="defuncion" id="defuncion" class="form-control">
                </div>
            </div>
        </div>

        <!-- Carnet, Teléfono y Email -->
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="carnet">Carné</label>
                    <input type="text" name="carnet" id="modalCarnet" class="form-control" required>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="modalTelefono" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="correo">Email</label>
                    <input type="email" name="correo" id="modalEmail" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Dirección -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea name="direccion" id="modalDireccion" rows="3" class="form-control" placeholder="Escribe la dirección completa"></textarea>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Script para calcular la edad automáticamente -->
<script>
   function calcularEdad(fechaNacimientoInputId, edadInputId) {
    var fechaNacimiento = new Date(document.getElementById(fechaNacimientoInputId).value);
    if (!isNaN(fechaNacimiento.getTime())) {
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        var mes = hoy.getMonth() - fechaNacimiento.getMonth();
        var dia = hoy.getDate() - fechaNacimiento.getDate();

        if (mes < 0 || (mes === 0 && dia < 0)) {
            edad--;
        }
        document.getElementById(edadInputId).value = edad < 0 ? 0 : edad;
    }
}

// Aquí usamos el ID correcto del input de fecha
document.getElementById('modalFnacimientol').addEventListener('change', function() {
    calcularEdad('modalFnacimientol', 'anios');
});

// Al cargar la página también
window.addEventListener('load', function() {
    calcularEdad('modalFnacimientol', 'anios');
});

</script>










