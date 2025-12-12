

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datosGenerales-tab" data-toggle="tab" href="#datosGenerales" role="tab" aria-controls="datosGenerales" aria-selected="true">Datos Generales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="direccion-tab" data-toggle="tab" href="#direccion" role="tab" aria-controls="direccion" aria-selected="false">Dirección</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="datosSalud-tab" data-toggle="tab" href="#datosSalud" role="tab" aria-controls="datosSalud" aria-selected="false">Datos de Salud</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="identificacion-tab" data-toggle="tab" href="#identificacion" role="tab" aria-controls="identificacion" aria-selected="false">Identificación</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="otros-tab" data-toggle="tab" href="#otros" role="tab" aria-controls="otros" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos Generales -->
        <div class="tab-pane fade show active" id="datosGenerales" role="tabpanel" aria-labelledby="datosGenerales-tab">
            <br>

            <div class="form-group">
                <label for="fotografia">Fotografía:</label>
                <input type="file" class="form-control-file" id="fotografia" name="fotografia" onchange="previewImage(event)">
                <img id="imagePreview" src="{{ $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" alt="Vista Previa" style="{{ $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>

            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="nombres" class=" col-form-label ">Nombres (*):</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres', $persona->nombres) }}" required>
                </div>
       
                <div class="col-sm-3">
                <label for="apellidos" class=" col-form-label">Apellidos (*):</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ old('apellidos', $persona->apellidos) }}" required>
                </div>
           
                <div class="col-sm-3">
                <label for="estado_civil">Estado Civil (*):</label>
                <select class="form-control" id="estado_civil" name="estado_civil" required>
                    <option value="">Seleccione el estado civil</option>
                    @foreach (\App\Models\Persona::getEstadoCivilOptions() as $estadocivil)
                            <option value="{{ $estadocivil }}" {{ old('estado_civil', $persona->estado_civil) == $estadocivil ? 'selected' : '' }}>
                                {{ $estadocivil }}
                            </option>
                        @endforeach
                </select> 
                </div>
            
                <div class="col-sm-3">
                <label for="apellidoCasada">Apellido Casada</label>
                <input type="text" class="form-control" id="apellidoCasada" name="apellido_casada"  value="{{ old('apellido_casada', $persona->apellido_casada)}}">
                </div>
            </div>
            <div class="form-group row">
               
            
                <div class="col-sm-2">
                <label for="fecha_nacimiento">Fecha de Nacimiento (*):</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"  value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento) }}" required>
                </div>
               
                <div class="col-sm-1">
                    <label for="anio">Edad:</label>
                    <input type="text" class="form-control" id="anio" name="anio" readonly>
                </div>

                <div class="col-sm-3">
                    <label for="lugar_nacimiento">Lugar de Nacimiento:</label>
                    <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="{{ old('lugar_nacimiento', $persona->lugar_nacimiento) }}">
                </div>

                <div class="col-sm-3">
                    <label for="nacionalidad">Nacionalidad (*):</label>
                    <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="{{ old('nacionalidad', $persona->nacionalidad) }}" required>
                </div>
           
                <div class="col-sm-2">

                <label for="genero">Género (*):</label>
                <select class="form-control" id="genero" name="genero" required>
                    <option value="">Seleccione género</option>
                    <option value="Masculino" {{ old('genero', $persona->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('genero', $persona->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old('genero', $persona->genero) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            </div>

    
           

            <div class="form-group row">

                <div class="col-sm-2">
                <label for="identificacion_documentos_id">Tipo de Identificación (*):</label>
                <select class="form-control" id="identificacion_documentos_id" name="identificacion_documentos_id" required>
                    <option value="">Seleccionar</option>
                    @foreach ($identificacionDocumentos as $doc)
                        <option value="{{ $doc->id }}" {{ old('identificacion_documentos_id', $persona->identificacion_documentos_id)  == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
          
                <div class="col-sm-3">
                <label for="num_documento">Número de Documento (*):</label>
                <input type="text" class="form-control" id="num_documento" name="num_documento" value="{{ old('num_documento', $persona->num_documento) }}" required>
                </div>

                <div class="col-sm-3">
                <label for="lugarExtendidoDocumento">Lugar de Emision del Documento</label>
                <input type="text" class="form-control" id="lugarEmision" name="lugar_emision" value="{{ old('nit',$persona->lugar_emision) }}">
                </div>  
      
                <div class="col-sm-2">
                <label for="nit">NIT (*):</label>
                <input type="text" class="form-control" id="nit" name="nit" value="{{ old('nit',$persona->nit) }}" required>
                 </div>  
            </div>



        </div>

        <!-- Dirección -->
        <div class="tab-pane fade" id="direccion" role="tabpanel" aria-labelledby="direccion-tab">
        <br>

            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="pais">País (*):</label>
                    <input type="text" class="form-control" id="pais" name="pais" value="{{ old('pais') }}">
                 </div>
                 <div class="col-sm-3">

                    <label for="departamento_id">Departamento:</label>
                <select name="departamento_id" id="departamento_id" class="form-control">
                    <option value="">Seleccione un departamento</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" {{ old('departamento_id', $persona->municipio->departamento_id) == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                    @endforeach
                </select>
                @error('departamento_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror


                 </div>

                  <div class="col-sm-3">
                    <label for="municipio_id">Municipio:</label>
                    <select name="municipio_id" id="municipio_id" class="form-control">
                        <option value="">Seleccione un municipio</option>
                    </select>
                    @error('municipio_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                  </div>


                  <div class="col-sm-2">

                    <label for="codigoPostal">Código Postal</label>
                    <input type="text" class="form-control" id="codigoPostal" name="codigoPostal" readonly>

                    </div>

            </div>
            
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="direccion">Dirección (*):</label>
       
                    <textarea class="form-control" id="direccion" name="direccion" rows="6" required>{{ old('direccion', $persona->direccion) }}</textarea>
                </div>

                <div class="col-sm-5">
                    <label for="email">Correo Electrónico (*):</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email',$persona->email) }}" required>
                </div>

                <div class="col-sm-3">
                    <label for="telefono_casa">Teléfono Casa:</label>
                    <input type="text" class="form-control" id="telefono_casa" name="telefono_casa" value="{{ old('telefono_casa',$persona->telefono_casa) }}">
                </div>

                <div class="col-sm-3">
                    <label for="telefono_mobil">Teléfono Móvil (*):</label>
                     <input type="text" class="form-control" id="telefono_mobil" name="telefono_mobil" value="{{ old('telefono_mobil',$persona->telefono_mobil) }}"required>
                </div>
            </div>
           
        </div>

        <!-- Datos de Salud -->
        <div class="tab-pane fade" id="datosSalud" role="tabpanel" aria-labelledby="datosSalud-tab">
            
            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="tipo_sangre">Tipo de Sangre:</label>
                        <select class="form-control" id="tipo_sangre" name="tipo_sangre">
                        <option value="">Seleccione el tipo de sangre</option>
                            @foreach (\App\Models\Persona::getTipoSangreOptions() as $tipo)
     
                                <option value="{{ $tipo }}" {{ old('tipo_sangre',$persona->tipo_sangre) == $tipo ? 'selected' : '' }}>
                                     {{ $tipo }}
                                </option>
                            @endforeach
                         </select>    
                </div>

          

            <div class="col-sm-3">
                <label for="alergias">Alergias:</label>
                <textarea class="form-control" id="alergias" rows="6"  name="alergias">{{ old('alergias', $persona->alergias) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="enfermedades">Enfermedades:</label>
                <textarea class="form-control" id="enfermedades" rows="6"  name="enfermedades">{{ old('enfermedades', $persona->enfermedades) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="medicamentos">Medicamentos:</label>
                <textarea class="form-control" id="medicamentos" rows="6"  name="medicamentos">{{ old('medicamentos', $persona->medicamentos) }}</textarea>
            </div>

        </div>

        </div>

        

        <!-- Identificación -->
        <div class="tab-pane fade" id="identificacion" role="tabpanel" aria-labelledby="identificacion-tab">
            
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="otros" role="tabpanel" aria-labelledby="otros-tab">
            <div class="form-group row">
                <div class="col-sm-3">
                <label for="fechaDefuncion">Fecha de Defunción</label>
                <input type="date" class="form-control" id="fechaDefuncion" name="fecha_defuncion" value="{{ old('fecha_nacimiento', $persona->fecha_defuncion) }}">
               </div>
               <div class="col-sm-3">
                <label for="pais_origen_id">País de Origen:</label>
                <select class="form-control" id="pais_origen_id" name="pais_origen_id">
                    <option value="">Seleccione un país</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}" {{ old('pais_origen_id',$persona->pais_origen_id) == $pais->id ? 'selected' : '' }}>
                            {{ $pais->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
         
          
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
</form>




<script>
    $(document).ready(function () {
        // Selecciona y activa el tab Datos Generales al cargar la página
        $('#myTab a[href="#datosGenerales"]').tab('show');
    });
</script>


<script>
    // Función para calcular la edad
    function calcularEdad(fechaNacimiento) {
        const fechaActual = new Date();
        const fechaNac = new Date(fechaNacimiento);
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
    document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        const fechaNacimiento = this.value;
        const edad = calcularEdad(fechaNacimiento);
        document.getElementById('anio').value = edad !== undefined ? edad : '';  // Mostrar edad o vacío si no hay fecha válida
    });

    // Si el formulario ya tiene una fecha de nacimiento al cargar la página
    window.onload = function() {
        const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
        if (fechaNacimiento) {
            const edad = calcularEdad(fechaNacimiento);
            document.getElementById('anio').value = edad;
            
            
          
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
    document.getElementById('departamento_id').addEventListener('change', function() {
        var departamentoId = this.value;
        var municipioSelect = document.getElementById('municipio_id');
        const codigoPostalInput = document.getElementById('codigoPostal');
        
      
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
            var departamentoId = document.getElementById('departamento_id').value;
            var municipioSelect = document.getElementById('municipio_id');
            const codigoPostalInput = document.getElementById('codigoPostal');
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
        const municipioSelect = document.getElementById('municipio_id');
        const codigoPostalInput = document.getElementById('codigoPostal');

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

