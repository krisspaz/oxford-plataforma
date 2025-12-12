

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datosGeneralesmadre-tab" data-toggle="tab" href="#datosGeneralesmadre" role="tab" aria-controls="datosGeneralesmadre" aria-selected="true">Datos Generales Madre</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" id="datosSaludmadre-tab" data-toggle="tab" href="#datosSaludmadre" role="tab" aria-controls="datosSaludmadre" aria-selected="false">Datos de Salud</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="identificacionmadre-tab" data-toggle="tab" href="#identificacionmadre" role="tab" aria-controls="identificacionmadre" aria-selected="false">Identificación</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="otrosmadre-tab" data-toggle="tab" href="#otrosmadre" role="tab" aria-controls="otrosmadre" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos Generales -->
        <div class="tab-pane fade show active" id="datosGeneralesmadre" role="tabpanel" aria-labelledby="datosGeneralesmadre-tab">
            <br>

            <div class="form-group">
                <label for="fotografiamadre">Fotografía:</label>
                <input type="file" class="form-control-file" id="fotografiamadre" name="fotografiamadre" onchange="previewImage(event)">
                <img id="imagePreview" src="{{ $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" alt="Vista Previa" style="{{ $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>

            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="nombresmadre" class=" col-form-label ">Nombres (*):</label>
                    <input type="text" class="form-control" id="nombresmadre" name="nombresmadre" value="{{ old('nombresmadre', $persona->nombres) }}" required>
                </div>
       
                <div class="col-sm-3">
                <label for="apellidosmadre" class=" col-form-label">Apellidos (*):</label>
                <input type="text" class="form-control" id="apellidosmadre" name="apellidosmadre" value="{{ old('apellidosmadre', $persona->apellidos) }}" required>
                </div>
           
                <div class="col-sm-3">
                <label for="estado_civilmadre">Estado Civil (*):</label>
                <select class="form-control" id="estado_civilmadre" name="estado_civilmadre" required>
                    <option value="">Seleccione el estado civil</option>
                    @foreach (\App\Models\Persona::getEstadoCivilOptions() as $estadocivil)
                            <option value="{{ $estadocivil }}" {{ old('estado_civilmadre', $persona->estado_civil) == $estadocivil ? 'selected' : '' }}>
                                {{ $estadocivil }}
                            </option>
                        @endforeach
                </select> 
                </div>
            
                <div class="col-sm-3">
                <label for="apellidoCasadamadre">Apellido Casada</label>
                <input type="text" class="form-control" id="apellidoCasadamadre" name="apellido_casadamadre"  value="{{ old('apellido_casadamadre', $persona->apellido_casada)}}">
                </div>
            </div>
            <div class="form-group row">
               
            
                <div class="col-sm-2">
                <label for="fecha_nacimientomadre">Fecha de Nacimiento (*):</label>
                <input type="date" class="form-control" id="fecha_nacimientomadre" name="fecha_nacimientomadre"  value="{{ old('fecha_nacimientomadre', $persona->fecha_nacimiento) }}" required>
                </div>
               
                <div class="col-sm-1">
                    <label for="aniomadre">Edad:</label>
                    <input type="text" class="form-control" id="aniomadre" name="aniomadre" readonly>
                </div>

                <div class="col-sm-3">
                    <label for="lugar_nacimientomadre">Lugar de Nacimiento:</label>
                    <input type="text" class="form-control" id="lugar_nacimientomadre" name="lugar_nacimientomadre" value="{{ old('lugar_nacimientomadre', $persona->lugar_nacimiento) }}">
                </div>

                <div class="col-sm-3">
                    <label for="nacionalidadmadre">Nacionalidad (*):</label>
                    <input type="text" class="form-control" id="nacionalidadmadre" name="nacionalidadmadre" value="{{ old('nacionalidadmadre', $persona->nacionalidad) }}" required>
                </div>
           
                <div class="col-sm-2">

                <label for="generomadre">Género (*):</label>
                <select class="form-control" id="generomadre" name="generomadre" required>
                    <option value="">Seleccione género</option>
                    <option value="Masculino" {{ old('generomadre', $persona->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('generomadre', $persona->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old('generomadre', $persona->genero) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            </div>

    
           

            <div class="form-group row">

                <div class="col-sm-2">
                <label for="identificacion_documentos_idmadre">Tipo de Identificación (*):</label>
                <select class="form-control" id="identificacion_documentos_idmadre" name="identificacion_documentos_idmadre" required>
                    <option value="">Seleccionar</option>
                    @foreach ($identificacionDocumentos as $doc)
                        <option value="{{ $doc->id }}" {{ old('identificacion_documentos_idmadre', $persona->identificacion_documentos_id)  == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
          
                <div class="col-sm-3">
                <label for="num_documentomadre">Número de Documento (*):</label>
                <input type="text" class="form-control" id="num_documentomadre" name="num_documentomadre" value="{{ old('num_documentomadre', $persona->num_documento) }}" required>
                </div>

                <div class="col-sm-3">
                <label for="lugarExtendidoDocumentomadre">Lugar de Emision del Documento</label>
                <input type="text" class="form-control" id="lugarEmisionmadre" name="lugar_emisionmadre" value="{{ old('lugar_emisionmadre',$persona->lugar_emision) }}">
                </div>  
      
                <div class="col-sm-2">
                <label for="nitmadre">NIT (*):</label>
                <input type="text" class="form-control" id="nitmadre" name="nitmadre" value="{{ old('nitmadre',$persona->nit) }}" required>
                 </div>  
            </div>



        </div>

    

        <!-- Datos de Salud -->
        <div class="tab-pane fade" id="datosSaludmadre" role="tabpanel" aria-labelledby="datosSaludmadre-tab">
            
            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="tipo_sangremadre">Tipo de Sangre:</label>
                        <select class="form-control" id="tipo_sangremadre" name="tipo_sangremadre">
                        <option value="">Seleccione el tipo de sangre</option>
                            @foreach (\App\Models\Persona::getTipoSangreOptions() as $tipo)
     
                                <option value="{{ $tipo }}" {{ old('tipo_sangremadre',$persona->tipo_sangre) == $tipo ? 'selected' : '' }}>
                                     {{ $tipo }}
                                </option>
                            @endforeach
                         </select>    
                </div>

          

            <div class="col-sm-3">
                <label for="alergiasmadre">Alergias:</label>
                <textarea class="form-control" id="alergiasmadre" rows="6"  name="alergiasmadre">{{ old('alergiasmadre', $persona->alergias) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="enfermedadesmadre">Enfermedades:</label>
                <textarea class="form-control" id="enfermedadesmadre" rows="6"  name="enfermedadesmadre">{{ old('enfermedadesmadre', $persona->enfermedades) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="medicamentosmadre">Medicamentos:</label>
                <textarea class="form-control" id="medicamentosmadre" rows="6"  name="medicamentosmadre">{{ old('medicamentosmadre', $persona->medicamentos) }}</textarea>
            </div>

        </div>

        </div>

        

        <!-- Identificación -->
        <div class="tab-pane fade" id="identificacionmadre" role="tabpanel" aria-labelledby="identificacionmadre-tab">
            
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="otrosmadre" role="tabpanel" aria-labelledby="otrosmadre-tab">
            <div class="form-group row">
                <div class="col-sm-3">
                <label for="fechaDefuncionmadre">Fecha de Defunción</label>
                <input type="date" class="form-control" id="fechaDefuncionmadre" name="fecha_defuncionmadre" value="{{ old('fecha_nacimientomadre', $persona->fecha_defuncion) }}">
               </div>
               <div class="col-sm-3">
                <label for="pais_origen_idmadre">País de Origen:</label>
                <select class="form-control" id="pais_origen_idmadre" name="pais_origen_idmadre">
                    <option value="">Seleccione un país</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}" {{ old('pais_origen_idmadre',$persona->pais_origen_id) == $pais->id ? 'selected' : '' }}>
                            {{ $pais->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
         
          
        </div>
    </div>

 




<script>
    $(document).ready(function () {
        // Selecciona y activa el tab Datos Generales al cargar la página
        $('#myTab a[href="#datosGeneralesmadre"]').tab('show');
    });
</script>


<script>
    // Función para calcular la edad
    function calcularEdad(fechaNacimiento) {
        const fechaActual = new Date();
        const fechaNac = new Date(fechaNacimientomadre);
        let edad = fechaActual.getFullYear() - fechaNac.getFullYear();
        const mes = fechaActual.getMonth() - fechaNac.getMonth();

        // Si el año de nacimiento es el mismo que el año actual
        if (edad === 0) {
            return 0;  // Si es el mismo año, la edad es 0
        }

        // Ajuste si aún no ha cumplido años este año
        if (mes < 0 || (mes === 0 && fechaActual.getDate() < fechaNac.getDate())) {
            edad--;
        }

        return edad;
    }

    // Evento cuando cambia la fecha de nacimiento
    document.getElementById('fecha_nacimientomadre').addEventListener('change', function() {
        const fechaNacimiento = this.value;
        const edad = calcularEdad(fechaNacimiento);
        document.getElementById('aniomadre').value = edad !== undefined ? edad : '';  // Mostrar edad o vacío si no hay fecha válida
    });

    // Si el formulario ya tiene una fecha de nacimiento al cargar la página
    window.onload = function() {
        const fechaNacimiento = document.getElementById('fecha_nacimientomadre').value;
        if (fechaNacimiento) {
            const edad = calcularEdad(fechaNacimiento);
            document.getElementById('aniomadre').value = edad;
            
            
          
        }
    };
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
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

<script>
    document.getElementById('departamento_idmadre').addEventListener('change', function() {
        var departamentoId = this.value;
        var municipioSelect = document.getElementById('municipio_idmadre');
        const codigoPostalInput = document.getElementById('codigoPostalmadre');
        
      
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';

        if (departamentoId) {
            fetch(`/personas/get-municipios/${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(municipio => {
                        var option = document.createElement('option');
                        option.value = municipio.id;
                        option.text = municipio.municipio;
                      
                        municipioSelect.add(option);
                       
                    });
                    
                });
        }
    });

     // Para cargar los municipios cuando se edita
     window.addEventListener('load', function() {
            var departamentoId = document.getElementById('departamento_idmadre').value;
            var municipioSelect = document.getElementById('municipio_idmadre');
            const codigoPostalInput = document.getElementById('codigoPostalmadre');
            if (departamentoId) {
                fetch(`/personas/get-municipios/${departamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(municipio => {
                            var option = document.createElement('option');
                            option.value = municipio.id;
                            option.text = municipio.municipio;
                           // 
                            
                            if (municipio.id == '{{ $persona->municipio_id }}') {
                                option.selected = true;
                                codigoPostalInput.value = municipio.codigo_postal;
                            }else{

                                codigoPostalInput.value = "Sin Codigo";


                            }
                            municipioSelect.add(option);
                            
                        });
                        
                    });
            }//fin if
            



        });

</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const municipioSelect = document.getElementById('municipio_idmadre');
        const codigoPostalInput = document.getElementById('codigoPostalmadre');

        municipioSelect.addEventListener('change', function() {
            const municipioId = this.value;
            if (municipioId) {
                // Realiza una solicitud AJAX para obtener el código postal
                fetch(`/get-codigo-postal/${municipioId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Actualiza el campo de código postal con la respuesta
                        if (data.codigopostal) {
                            codigoPostalInput.value = data.codigopostal;
                        } else {
                            codigoPostalInput.value = 'No Existe';
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener el código postal:', error);
                        codigoPostalInput.value = 'No Existe';
                    });
            } else {
                codigoPostalInput.value = 'No Existe 3';
            }//fin if y else
        });
    });
</script>

