

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datosGeneralesencargado-tab" data-toggle="tab" href="#datosGeneralesencargado" role="tab" aria-controls="datosGeneralesencargado" aria-selected="true">Datos Generales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="direccionencargado-tab" data-toggle="tab" href="#direccionencargado" role="tab" aria-controls="direccionencargado" aria-selected="false">Dirección</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="datosSaludencargado-tab" data-toggle="tab" href="#datosSaludencargado" role="tab" aria-controls="datosSaludencargado" aria-selected="false">Datos de Salud</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="identificacionencargado-tab" data-toggle="tab" href="#identificacionencargado" role="tab" aria-controls="identificacionencargado" aria-selected="false">Identificación</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="otrosencargado-tab" data-toggle="tab" href="#otrosencargado" role="tab" aria-controls="otrosencargado" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos Generales -->
        <div class="tab-pane fade show active" id="datosGeneralesencargado" role="tabpanel" aria-labelledby="datosGeneralesencargado-tab">
            <br>

            <div class="form-group">
                <label for="fotografiaencargado">Fotografía:</label>
                <input type="file" class="form-control-file" id="fotografiaencargado" name="fotografiaencargado" onchange="previewImage(event)">
                <img id="imagePreview" src="{{ $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" alt="Vista Previa" style="{{ $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>

            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="nombresencargado" class=" col-form-label ">Nombres (*):</label>
                    <input type="text" class="form-control" id="nombresencargados" name="nombresencargados" value="{{ old('nombresencargados', $persona->nombres) }}" required>
                </div>
       
                <div class="col-sm-3">
                <label for="apellidosencargados" class=" col-form-label">Apellidos (*):</label>
                <input type="text" class="form-control" id="apellidosencargados" name="apellidosencargados" value="{{ old('apellidosencargados', $persona->apellidos) }}" required>
                </div>
           
                <div class="col-sm-3">
                <label for="estado_civilencargados">Estado Civil (*):</label>
                <select class="form-control" id="estado_civilencargados" name="estado_civilencargados" required>
                    <option value="">Seleccione el estado civil</option>
                    @foreach (\App\Models\Persona::getEstadoCivilOptions() as $estadocivil)
                            <option value="{{ $estadocivil }}" {{ old('estado_civilencargados', $persona->estado_civil) == $estadocivil ? 'selected' : '' }}>
                                {{ $estadocivil }}
                            </option>
                        @endforeach
                </select> 
                </div>
            
                <div class="col-sm-3">
                <label for="apellidoCasadaencargado">Apellido Casada</label>
                <input type="text" class="form-control" id="apellidoCasadaencargado" name="apellido_casadaencargado"  value="{{ old('apellido_casadaencargado', $persona->apellido_casada)}}">
                </div>
            </div>
            <div class="form-group row">
               
            
                <div class="col-sm-2">
                <label for="fecha_nacimientoencargado">Fecha de Nacimiento (*):</label>
                <input type="date" class="form-control" id="fecha_nacimientoencargado" name="fecha_nacimientoencargado"  value="{{ old('fecha_nacimientoencargado', $persona->fecha_nacimiento) }}" required>
                </div>
               
                <div class="col-sm-1">
                    <label for="anioencargado">Edad:</label>
                    <input type="text" class="form-control" id="anioencargado" name="anioencargado" readonly>
                </div>

                <div class="col-sm-3">
                    <label for="lugar_nacimientoencargado">Lugar de Nacimiento:</label>
                    <input type="text" class="form-control" id="lugar_nacimientoencargado" name="lugar_nacimientoencargado" value="{{ old('lugar_nacimientoencargado', $persona->lugar_nacimiento) }}">
                </div>

                <div class="col-sm-3">
                    <label for="nacionalidadencargado">Nacionalidad (*):</label>
                    <input type="text" class="form-control" id="nacionalidadencargado" name="nacionalidadencargado" value="{{ old('nacionalidadencargado', $persona->nacionalidad) }}" required>
                </div>
           
                <div class="col-sm-2">

                <label for="generoencargado">Género (*):</label>
                <select class="form-control" id="generoencargado" name="generoencargado" required>
                    <option value="">Seleccione género</option>
                    <option value="Masculino" {{ old('generoencargado', $persona->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('generoencargado', $persona->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old('generoencargado', $persona->genero) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            </div>

    
           

            <div class="form-group row">

                <div class="col-sm-2">
                <label for="identificacion_documentos_idencargado">Tipo de Identificación (*):</label>
                <select class="form-control" id="identificacion_documentos_idencargado" name="identificacion_documentos_idencargado" required>
                    <option value="">Seleccionar</option>
                    @foreach ($identificacionDocumentos as $doc)
                        <option value="{{ $doc->id }}" {{ old('identificacion_documentos_idencargado', $persona->identificacion_documentos_id)  == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
          
                <div class="col-sm-3">
                <label for="num_documentoencargado">Número de Documento (*):</label>
                <input type="text" class="form-control" id="num_documentoencargado" name="num_documentoencargado" value="{{ old('num_documentoencargado', $persona->num_documento) }}" required>
                </div>

                <div class="col-sm-3">
                <label for="lugarExtendidoDocumentoencargado">Lugar de Emision del Documento</label>
                <input type="text" class="form-control" id="lugarEmisionencargado" name="lugar_emisionencargado" value="{{ old('lugar_emisionencargado',$persona->lugar_emision) }}">
                </div>  
      
                <div class="col-sm-2">
                <label for="nitencargado">NIT (*):</label>
                <input type="text" class="form-control" id="nitencargado" name="nitencargado" value="{{ old('nitencargado',$persona->nit) }}" required>
                 </div>  
            </div>



        </div>

        <!-- Dirección -->
        <div class="tab-pane fade" id="direccionencargado" role="tabpanel" aria-labelledby="direccionencargado-tab">
        <br>

            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="paisencargado">País (*):</label>
                    <input type="text" class="form-control" id="paisencargado" name="paisencargado" value="{{ old('paisencargado') }}">
                 </div>
                 <div class="col-sm-3">

                    <label for="departamento_idencargado">Departamento:</label>
                <select name="departamento_idencargado" id="departamento_idencargado" class="form-control">
                    <option value="">Seleccione un departamento</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" {{ old('departamento_idencargado', $persona->municipio->departamento_id) == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                    @endforeach
                </select>
                @error('departamento_idencargado')
                    <div class="text-danger">{{ $message }}</div>
                @enderror


                 </div>

                  <div class="col-sm-3">
                    <label for="municipio_idencargado">Municipio:</label>
                    <select name="municipio_idencargado" id="municipio_idencargado" class="form-control">
                        <option value="">Seleccione un municipio</option>
                    </select>
                    @error('municipio_idencargado')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                  </div>


                  <div class="col-sm-2">

                    <label for="codigoPostalencargado">Código Postal</label>
                    <input type="text" class="form-control" id="codigoPostalencargado" name="codigoPostalencargado" readonly>

                    </div>

            </div>
            
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="direccionencargado">Dirección (*):</label>
       
                    <textarea class="form-control" id="direccionencargado" name="direccionencargado" rows="6" required>{{ old('direccionencargado', $persona->direccion) }}</textarea>
                </div>

                <div class="col-sm-5">
                    <label for="emailencargado">Correo Electrónico (*):</label>
                    <input type="emailencargado" class="form-control" id="emailencargado" name="emailencargado" value="{{ old('emailencargado',$persona->email) }}" required>
                </div>

                <div class="col-sm-3">
                    <label for="telefono_casaencargado">Teléfono Casa:</label>
                    <input type="text" class="form-control" id="telefono_casaencargado" name="telefono_casaencargado" value="{{ old('telefono_casaencargado',$persona->telefono_casa) }}">
                </div>

                <div class="col-sm-3">
                    <label for="telefono_mobilencargado">Teléfono Móvil (*):</label>
                     <input type="text" class="form-control" id="telefono_mobilencargado" name="telefono_mobilencargado" value="{{ old('telefono_mobilencargado',$persona->telefono_mobil) }}"required>
                </div>
            </div>
           
        </div>

        <!-- Datos de Salud -->
        <div class="tab-pane fade" id="datosSaludencargado" role="tabpanel" aria-labelledby="datosSaludencargado-tab">
            
            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="tipo_sangreencargado">Tipo de Sangre:</label>
                        <select class="form-control" id="tipo_sangreencargado" name="tipo_sangreencargado">
                        <option value="">Seleccione el tipo de sangre</option>
                            @foreach (\App\Models\Persona::getTipoSangreOptions() as $tipo)
     
                                <option value="{{ $tipo }}" {{ old('tipo_sangreencargado',$persona->tipo_sangre) == $tipo ? 'selected' : '' }}>
                                     {{ $tipo }}
                                </option>
                            @endforeach
                         </select>    
                </div>

          

            <div class="col-sm-3">
                <label for="alergiasencargado">Alergias:</label>
                <textarea class="form-control" id="alergiasencargado" rows="6"  name="alergiasencargado">{{ old('alergiasencargado', $persona->alergias) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="enfermedadesencargado">Enfermedades:</label>
                <textarea class="form-control" id="enfermedadesencargado" rows="6"  name="enfermedadesencargado">{{ old('enfermedadesencargado', $persona->enfermedades) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="medicamentosencargado">Medicamentos:</label>
                <textarea class="form-control" id="medicamentosencargado" rows="6"  name="medicamentosencargado">{{ old('medicamentosencargado', $persona->medicamentos) }}</textarea>
            </div>

        </div>

        </div>

        

        <!-- Identificación -->
        <div class="tab-pane fade" id="identificacionencargado" role="tabpanel" aria-labelledby="identificacionencargado-tab">
            
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="otrosencargado" role="tabpanel" aria-labelledby="otrosencargado-tab">
            <div class="form-group row">
                <div class="col-sm-3">
                <label for="fechaDefuncionencargado">Fecha de Defunción</label>
                <input type="date" class="form-control" id="fechaDefuncionencargado" name="fecha_defuncionencargado" value="{{ old('fecha_nacimientoencargado', $persona->fecha_defuncion) }}">
               </div>
               <div class="col-sm-3">
                <label for="pais_origen_idencargado">País de Origen:</label>
                <select class="form-control" id="pais_origen_idencargado" name="pais_origen_idencargado">
                    <option value="">Seleccione un país</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}" {{ old('pais_origen_idencargado',$persona->pais_origen_id) == $pais->id ? 'selected' : '' }}>
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
        $('#myTab a[href="#datosGeneralesencargado"]').tab('show');
    });
</script>


<script>
    // Función para calcular la edad
    function calcularEdad(fechaNacimientoencargado) {
        const fechaActual = new Date();
        const fechaNac = new Date(fechaNacimientoencargado);
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
    document.getElementById('fecha_nacimientoencargado').addEventListener('change', function() {
        const fechaNacimiento = this.value;
        const edad = calcularEdad(fechaNacimiento);
        document.getElementById('anioencargado').value = edad !== undefined ? edad : '';  // Mostrar edad o vacío si no hay fecha válida
    });

    // Si el formulario ya tiene una fecha de nacimiento al cargar la página
    window.onload = function() {
        const fechaNacimiento = document.getElementById('fecha_nacimientoencargado').value;
        if (fechaNacimiento) {
            const edad = calcularEdad(fechaNacimiento);
            document.getElementById('anioencargado').value = edad;
            
            
          
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
    document.getElementById('departamento_idencargado').addEventListener('change', function() {
        var departamentoId = this.value;
        var municipioSelect = document.getElementById('municipio_idencargado');
        const codigoPostalInput = document.getElementById('codigoPostalencargado');
        
      
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
            var departamentoId = document.getElementById('departamento_idencargado').value;
            var municipioSelect = document.getElementById('municipio_idencargado');
            const codigoPostalInput = document.getElementById('codigoPostalencargado');
            if (departamentoId) {
                fetch(`/personas/get-municipios/${departamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(municipio => {
                            var option = document.createElement('option');
                            option.value = municipio.id;
                            option.text = municipio.municipio;
                           // 
                            
                            if (municipio.id == '{{ $persona->municipio_idencargado }}') {
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
        const municipioSelect = document.getElementById('municipio_idencargado');
        const codigoPostalInput = document.getElementById('codigoPostalencargado');

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

