
<style>
    /* Un poco de estilo para los botones */
    .btn-eliminar {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
    }
</style>

<div class="container">

    @if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
    <h2>Documentos de Inscripción</h2>

    <div id="documentosContainer">
        <div class="documento">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="tipo_documento[]">Tipo de Documento</label>
                    <select name="tipo_documento[]" class="form-control tipo_documento" onchange="mostrarCampoOtro(this)" required>
                        <option value="Certificado" {{ old('tipo_documento.0') == 'Certificado' ? 'selected' : '' }}>Certificado</option>
                        <option value="Acta" {{ old('tipo_documento.0') == 'Acta' ? 'selected' : '' }}>Acta</option>
                        <option value="Constancia" {{ old('tipo_documento.0') == 'Constancia' ? 'selected' : '' }}>Constancia</option>
                        <option value="Otro" {{ old('tipo_documento.0') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <input type="text" name="otro_documento[]" class="form-control otro_documento" placeholder="Especifique el documento" value="{{ old('otro_documento.0') }}" style="display: {{ old('tipo_documento.0') == 'Otro' ? 'block' : 'none' }};">
                </div>
                <div class="form-group col-md-2">
                    <label for="nombre_documento[]">Nombre del Documento</label>
                    <input type="text" name="nombre_documento[]" class="form-control" required value="{{ old('nombre_documento.0') }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="documento[]">Subir Documento</label>
                    <input type="file" name="documento[]" class="form-control" accept=".pdf" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="fexpiracion[]">Fecha de Expiración</label>
                    <input type="date" name="fexpiracion[]" class="form-control" value="{{ old('fexpiracion.0') }}">
                </div>
            </div>
            <button type="button" class="btn btn-danger" onclick="eliminarDocumento(this)">Eliminar</button>
            <hr>
        </div>
    </div>

    <button type="button" onclick="agregarDocumento()" class="btn btn-primary">Añadir otro documento</button>
    <br><br>
</div>

<script>
    // Función para añadir un nuevo bloque de documentos
    function agregarDocumento() {
        const contenedor = document.getElementById('documentosContainer');
        const nuevoDocumento = document.createElement('div');
        nuevoDocumento.classList.add('documento');

        // Estructura HTML del nuevo bloque
        nuevoDocumento.innerHTML = `
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="tipo_documento[]">Tipo de Documento</label>
                    <select name="tipo_documento[]" class="form-control tipo_documento" onchange="mostrarCampoOtro(this)">
                        <option value="Certificado">Certificado</option>
                        <option value="Acta">Acta</option>
                        <option value="Constancia">Constancia</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <input type="text" name="otro_documento[]" class="form-control otro_documento" placeholder="Especifique el documento" style="display: none;">
                </div>
                <div class="form-group col-md-2">
                    <label for="nombre_documento[]">Nombre del Documento</label>
                    <input type="text" name="nombre_documento[]" class="form-control" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="documento[]">Subir Documento</label>
                    <input type="file" name="documento[]" class="form-control" accept=".pdf" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="fexpiracion[]">Fecha de Expiración</label>
                    <input type="date" name="fexpiracion[]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-danger" onclick="eliminarDocumento(this)">Eliminar</button>
            <hr>
        `;

        contenedor.appendChild(nuevoDocumento);
    }

    // Función para mostrar el campo "Otro" en el tipo de documento
    function mostrarCampoOtro(selectElement) {
        const otroInput = selectElement.parentElement.querySelector('.otro_documento');
        if (selectElement.value === 'Otro') {
            otroInput.style.display = 'block';
            otroInput.setAttribute('required', 'required');
        } else {
            otroInput.style.display = 'none';
            otroInput.removeAttribute('required');
        }
    }

    // Función para eliminar un bloque de documento
    function eliminarDocumento(button) {
        const documentoDiv = button.closest('.documento'); // Encuentra el div contenedor del documento
        documentoDiv.remove(); // Elimina ese bloque específico del DOM
    }
</script>
