

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="{{ $prefix }}datosGenerales-tab" data-toggle="tab" href="#{{ $prefix }}datosGenerales" role="tab" aria-controls="{{ $prefix }}datosGenerales" aria-selected="true">Datos Generales {{ $prefix }}</a>
        </li>
      
        <li class="nav-item">
            <a class="nav-link" id="{{ $prefix }}datosSalud-tab" data-toggle="tab" href="#{{ $prefix }}datosSalud" role="tab" aria-controls="{{ $prefix }}datosSalud" aria-selected="false">Datos de Salud</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="{{ $prefix }}identificacion-tab" data-toggle="tab" href="#{{ $prefix }}identificacion" role="tab" aria-controls="{{ $prefix }}identificacion" aria-selected="false">Identificación</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="{{ $prefix }}otros-tab" data-toggle="tab" href="#{{ $prefix }}otros" role="tab" aria-controls="{{ $prefix }}otros" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos Generales -->
        <div class="tab-pane fade show active" id="{{ $prefix }}datosGenerales" role="tabpanel" aria-labelledby="{{ $prefix }}datosGenerales-tab">
            <br>

            <div class="form-group">
                <label for="{{ $prefix }}[fotografia]">Fotografía:</label>
                <input type="file" class="form-control-file" id="{{ $prefix }}[fotografia]" name="{{ $prefix }}[fotografia]" onchange="previewImage(event)">
                <img id="imagePreview" src="{{ $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" alt="Vista Previa" style="{{ $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>

            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="{{ $prefix }}[nombres]" class=" col-form-label ">Nombres (*):</label>
                    <input type="text" class="form-control" id="{{ $prefix }}[nombres]" name="{{ $prefix }}[nombres]" value="{{ old($prefix.'.nombres', $persona->nombres) }}" required>
                </div>
       
                <div class="col-sm-3">
                <label for="{{ $prefix }}[apellidos]" class=" col-form-label">Apellidos (*):</label>
                <input type="text" class="form-control" id="{{ $prefix }}[apellidos]" name="{{ $prefix }}[apellidos]" value="{{ old($prefix.'apellidos', $persona->apellidos) }}" required>
                </div>
           
                <div class="col-sm-3">
                <label for="{{ $prefix }}[estado_civil]">Estado Civil (*):</label>
                <select class="form-control" id="{{ $prefix }}[estado_civil]" name="{{ $prefix }}[estado_civil]" required>
                    <option value="">Seleccione el estado civil</option>
                    @foreach (\App\Models\Persona::getEstadoCivilOptions() as $estadocivil)
                            <option value="{{ $estadocivil }}" {{ old($prefix.'.estado_civil', $persona->estado_civil) == $estadocivil ? 'selected' : '' }}>
                                {{ $estadocivil }}
                            </option>
                        @endforeach
                </select> 
                </div>
            
                <div class="col-sm-3">
                <label for="{{ $prefix }}[apellidoCasada]">Apellido Casada</label>
                <input type="text" class="form-control" id="{{ $prefix }}[apellidoCasada]" name="{{ $prefix }}[apellido_casada]"  value="{{ old($prefix.'.apellido_casada', $persona->apellido_casada)}}">
                </div>
            </div>
            <div class="form-group row">
               
            
                <div class="col-sm-2">
                <label for="{{ $prefix }}[fecha_nacimiento]">Fecha de Nacimiento (*):</label>
                <input type="date" class="form-control" id="{{ $prefix }}[fecha_nacimiento]" name="{{ $prefix }}[fecha_nacimiento]"  value="{{ old($prefix.'.fecha_nacimiento', $persona->fecha_nacimiento) }}" required>
                </div>
               
                <div class="col-sm-1">
                    <label for="{{ $prefix }}[anio]">Edad:</label>
                    <input type="text" class="form-control" id="{{ $prefix }}[anio]" name="{{ $prefix }}[anio]" readonly>
                </div>

                <div class="col-sm-3">
                    <label for="{{ $prefix }}[lugar_nacimiento]">Lugar de Nacimiento:</label>
                    <input type="text" class="form-control" id="{{ $prefix }}[lugar_nacimiento]" name="{{ $prefix }}[lugar_nacimiento]" value="{{ old($prefix.'.lugar_nacimiento', $persona->lugar_nacimiento) }}">
                </div>

                <div class="col-sm-3">
                    <label for="{{ $prefix }}[nacionalidad]">Nacionalidad (*):</label>
                    <input type="text" class="form-control" id="{{ $prefix }}[nacionalidad]" name="{{ $prefix }}[nacionalidad]" value="{{ old($prefix.'.nacionalidad', $persona->nacionalidad) }}" required>
                </div>
           
                <div class="col-sm-2">

                <label for="{{ $prefix }}[genero]">Género (*):</label>
                <select class="form-control" id="{{ $prefix }}[genero]" name="{{ $prefix }}[genero]" required>
                    <option value="">Seleccione género</option>
                    <option value="Masculino" {{ old($prefix.'.genero', $persona->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old($prefix.'.genero', $persona->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old($prefix.'.genero', $persona->genero) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            </div>

    
           

            <div class="form-group row">

                <div class="col-sm-2">
                <label for="{{ $prefix }}[identificacion_documentos_id]">Tipo de Identificación (*):</label>
                <select class="form-control" id="{{ $prefix }}[identificacion_documentos_id]" name="{{ $prefix }}[identificacion_documentos_id]" required>
                    <option value="">Seleccionar</option>
                    @foreach ($identificacionDocumentos as $doc)
                        <option value="{{ $doc->id }}" {{ old($prefix.'.identificacion_documentos_id', $persona->identificacion_documentos_id)  == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
          
                <div class="col-sm-3">
                <label for="{{ $prefix }}[num_documento]">Número de Documento (*):</label>
                <input type="text" class="form-control" id="{{ $prefix }}[num_documento]" name="{{ $prefix }}[num_documento]" value="{{ old($prefix.'.num_documento', $persona->num_documento) }}" required>
                </div>

                <div class="col-sm-3">
                <label for="{{ $prefix }}[lugarExtendidoDocumento]">Lugar de Emision del Documento</label>
                <input type="text" class="form-control" id="{{ $prefix }}[lugarEmision]" name="{{ $prefix }}[lugar_emision]" value="{{ old($prefix.'.lugar_emision',$persona->lugar_emision) }}">
                </div>  
      
                <div class="col-sm-2">
                <label for="{{ $prefix }}[nit]">NIT (*):</label>
                <input type="text" class="form-control" id="{{ $prefix }}[nit]" name="{{ $prefix }}[nit]" value="{{ old($prefix.'.nit',$persona->nit) }}" required>
                 </div>  
            </div>



        </div>

      

        <!-- Datos de Salud -->
        <div class="tab-pane fade" id="{{ $prefix }}datosSalud" role="tabpanel" aria-labelledby="{{ $prefix }}datosSalud-tab">
            
            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="{{ $prefix }}[tipo_sangre]">Tipo de Sangre:</label>
                        <select class="form-control" id="{{ $prefix }}[tipo_sangre]" name="{{ $prefix }}[tipo_sangre]">
                        <option value="">Seleccione el tipo de sangre</option>
                            @foreach (\App\Models\Persona::getTipoSangreOptions() as $tipo)
     
                                <option value="{{ $tipo }}" {{ old($prefix.'.tipo_sangre',$persona->tipo_sangre) == $tipo ? 'selected' : '' }}>
                                     {{ $tipo }}
                                </option>
                            @endforeach
                         </select>    
                </div>

          

            <div class="col-sm-3">
                <label for="{{ $prefix }}[alergias]">Alergias:</label>
                <textarea class="form-control" id="{{ $prefix }}[alergias]" rows="6"  name="{{ $prefix }}[alergias]">{{ old($prefix.'.alergias', $persona->alergias) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="{{ $prefix }}[enfermedades]">Enfermedades:</label>
                <textarea class="form-control" id="{{ $prefix }}[enfermedades]" rows="6"  name="{{ $prefix }}[enfermedades]">{{ old($prefix.'.enfermedades', $persona->enfermedades) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="{{ $prefix }}[medicamentos]">Medicamentos:</label>
                <textarea class="form-control" id="{{ $prefix }}[medicamentos]" rows="6"  name="{{ $prefix }}[medicamentos]">{{ old($prefix.'.medicamentos', $persona->medicamentos) }}</textarea>
            </div>

        </div>

        </div>

        

        <!-- Identificación -->
        <div class="tab-pane fade" id="{{ $prefix }}identificacion" role="tabpanel" aria-labelledby="{{ $prefix }}identificacion-tab">
            
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="{{ $prefix }}otros" role="tabpanel" aria-labelledby="{{ $prefix }}otros-tab">
            <div class="form-group row">
                <div class="col-sm-3">
                <label for="{{ $prefix }}[fechaDefuncion]">Fecha de Defunción</label>
                <input type="date" class="form-control" id="{{ $prefix }}[fechaDefuncion]" name="{{ $prefix }}[fecha_defuncion]" value="{{ old($prefix.'.fecha_nacimiento', $persona->fecha_defuncion) }}">
               </div>
               <div class="col-sm-3">
                <label for="{{ $prefix }}[pais_origen_id]">País de Origen:</label>
                <select class="form-control" id="{{ $prefix }}[pais_origen_id]" name="{{ $prefix }}[pais_origen_id]">
                    <option value="">Seleccione un país</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}" {{ old($prefix.'.pais_origen_id',$persona->pais_origen_id) == $pais->id ? 'selected' : '' }}>
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
        $('#myTab a[href="#{{ $prefix }}datosGenerales"]').tab('show');
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
    document.getElementById('{{ $prefix }}[fecha_nacimiento]').addEventListener('change', function() {
        const fechaNacimiento = this.value;
        const edad = calcularEdad(fechaNacimiento);
        document.getElementById('{{ $prefix }}[anio]').value = edad !== undefined ? edad : '';  // Mostrar edad o vacío si no hay fecha válida
    });

    // Si el formulario ya tiene una fecha de nacimiento al cargar la página
    window.onload = function() {
        const fechaNacimiento = document.getElementById('{{ $prefix }}[fecha_nacimiento]').value;
        if (fechaNacimiento) {
            const edad = calcularEdad(fechaNacimiento);
            document.getElementById('{{ $prefix }}[anio]').value = edad;
            
            
          
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
    document.getElementById('{{ $prefix }}[departamento_id]').addEventListener('change', function() {
        var departamentoId = this.value;
        var municipioSelect = document.getElementById('{{ $prefix }}[municipio_id]');
        const codigoPostalInput = document.getElementById('{{ $prefix }}[codigoPostal]');
        
      
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
            var departamentoId = document.getElementById('{{ $prefix }}[departamento_id]').value;
            var municipioSelect = document.getElementById('{{ $prefix }}[municipio_id]');
            const codigoPostalInput = document.getElementById('{{ $prefix }}[codigoPostal]');
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
        const municipioSelect = document.getElementById('{{ $prefix }}[municipio_id]');
        const codigoPostalInput = document.getElementById('{{ $prefix }}[codigoPostal]');

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
