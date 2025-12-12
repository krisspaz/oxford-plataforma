

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datosGeneralespadre-tab" data-toggle="tab" href="#datosGeneralespadre" role="tab" aria-controls="datosGeneralespadre" aria-selected="true">Datos Generales</a>
        </li>
      
        <li class="nav-item">
            <a class="nav-link" id="datosSaludpadre-tab" data-toggle="tab" href="#datosSaludpadre" role="tab" aria-controls="datosSaludpadre" aria-selected="false">Datos de Salud</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="identificacionpadre-tab" data-toggle="tab" href="#identificacionpadre" role="tab" aria-controls="identificacionpadre" aria-selected="false">Identificación</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="otrospadre-tab" data-toggle="tab" href="#otrospadre" role="tab" aria-controls="otrospadre" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos Generales -->
        <div class="tab-pane fade show active" id="datosGeneralespadre" role="tabpanel" aria-labelledby="datosGeneralespadre-tab">
            <br>

            <div class="form-group">
                <label for="fotografia_padre">Fotografía:</label>
                <input type="file" class="form-control-file" id="fotografia_padre" name="fotografia_padre" onchange="previewImage(event)">
                <img id="imagePreview" src="{{ $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}" alt="Vista Previa" style="{{ $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
            </div>

            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="nombres_padre" class=" col-form-label ">Nombres (*):</label>
                    <input type="text" class="form-control" id="nombres_padre" name="nombres_padre" value="{{ old('nombres_padre', $persona->nombres) }}" required>
                </div>
       
                <div class="col-sm-3">
                <label for="apellidos_padre" class=" col-form-label">Apellidos (*):</label>
                <input type="text" class="form-control" id="apellidos_padre" name="apellidos_padre" value="{{ old('apellidos_padre', $persona->apellidos) }}" required>
                </div>
           
                <div class="col-sm-3">
                <label for="estado_civil_padre">Estado Civil (*):</label>
                <select class="form-control" id="estado_civil_padre" name="estado_civil_padre" required>
                    <option value="">Seleccione el estado civil</option>
                    @foreach (\App\Models\Persona::getEstadoCivilOptions() as $estadocivil)
                            <option value="{{ $estadocivil }}" {{ old('estado_civil_padre', $persona->estado_civil) == $estadocivil ? 'selected' : '' }}>
                                {{ $estadocivil }}
                            </option>
                        @endforeach
                </select> 
                </div>
            
               
            </div>
            <div class="form-group row">
               
            
                <div class="col-sm-2">
                <label for="fecha_nacimiento_padre">Fecha de Nacimiento (*):</label>
                <input type="date" class="form-control" id="fecha_nacimiento_padre" name="fecha_nacimiento_padre"  value="{{ old('fecha_nacimiento_padre', $persona->fecha_nacimiento) }}" required>
                </div>
               
                <div class="col-sm-1">
                    <label for="anio_padre">Edad:</label>
                    <input type="text" class="form-control" id="anio_padre" name="anio_padre" readonly>
                </div>

                <div class="col-sm-3">
                    <label for="lugar_nacimiento_padre">Lugar de Nacimiento:</label>
                    <input type="text" class="form-control" id="lugar_nacimiento_padre" name="lugar_nacimiento_padre" value="{{ old('lugar_nacimiento_padre', $persona->lugar_nacimiento) }}">
                </div>

                <div class="col-sm-3">
                    <label for="nacionalidad_padre">Nacionalidad (*):</label>
                    <input type="text" class="form-control" id="nacionalidad_padre" name="nacionalidad_padre" value="{{ old('nacionalidad_padre', $persona->nacionalidad) }}" required>
                </div>
           
                <div class="col-sm-2">

                <label for="genero_padre">Género (*):</label>
                <select class="form-control" id="genero_padre" name="genero_padre" required>
                    <option value="">Seleccione género</option>
                    <option value="Masculino" {{ old('genero_padre', $persona->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('genero_padre', $persona->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old('genero_padre', $persona->genero) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            </div>

    
           

            <div class="form-group row">

                <div class="col-sm-2">
                <label for="identificacion_documentos_id_padre">Tipo de Identificación (*):</label>
                <select class="form-control" id="identificacion_documentos_id_padre" name="identificacion_documentos_id_padre" required>
                    <option value="">Seleccionar</option>
                    @foreach ($identificacionDocumentos as $doc)
                        <option value="{{ $doc->id }}" {{ old('identificacion_documentos_id_padre', $persona->identificacion_documentos_id)  == $doc->id ? 'selected' : '' }}>
                            {{ $doc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
          
                <div class="col-sm-3">
                <label for="num_documento_padre">Número de Documento (*):</label>
                <input type="text" class="form-control" id="num_documento_padre" name="num_documento_padre" value="{{ old('num_documento_padre', $persona->num_documento) }}" required>
                </div>

                <div class="col-sm-3">
                <label for="lugarExtendidoDocumento_padre">Lugar de Emision del Documento</label>
                <input type="text" class="form-control" id="lugarEmision_padre" name="lugar_emision_padre" value="{{ old('lugar_emision_padre',$persona->lugar_emision) }}">
                </div>  
      
                <div class="col-sm-2">
                <label for="nit_padre">NIT (*):</label>
                <input type="text" class="form-control" id="nit_padre" name="nit_padre" value="{{ old('nit_padre',$persona->nit) }}" required>
                 </div>  
            </div>



        </div>

      

        <!-- Datos de Salud -->
        <div class="tab-pane fade" id="datosSaludpadre" role="tabpanel" aria-labelledby="datosSaludpadre-tab">
            
            <div class="form-group row">
               
                <div class="col-sm-3">
                    <label for="tipo_sangre_padre">Tipo de Sangre:</label>
                        <select class="form-control" id="tipo_sangre_padre" name="tipo_sangre_padre">
                        <option value="">Seleccione el tipo de sangre</option>
                            @foreach (\App\Models\Persona::getTipoSangreOptions() as $tipo)
     
                                <option value="{{ $tipo }}" {{ old('tipo_sangre_padre',$persona->tipo_sangre) == $tipo ? 'selected' : '' }}>
                                     {{ $tipo }}
                                </option>
                            @endforeach
                         </select>    
                </div>

          

            <div class="col-sm-3">
                <label for="alergias_padre">Alergias:</label>
                <textarea class="form-control" id="alergias_padre" rows="6"  name="alergias_padre">{{ old('alergias_padre', $persona->alergias) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="enfermedades_padre">Enfermedades:</label>
                <textarea class="form-control" id="enfermedades_padre" rows="6"  name="enfermedades_padre">{{ old('enfermedades_padre', $persona->enfermedades) }}</textarea>
            </div>
            <div class="col-sm-3">
                <label for="medicamentos_padre">Medicamentos:</label>
                <textarea class="form-control" id="medicamentos_padre" rows="6"  name="medicamentos_padre">{{ old('medicamentos_padre', $persona->medicamentos) }}</textarea>
            </div>

        </div>

        </div>

        

        <!-- Identificación -->
        <div class="tab-pane fade" id="identificacionpadre" role="tabpanel" aria-labelledby="identificacionpadre-tab">
            
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="otrospadre" role="tabpanel" aria-labelledby="otrospadre-tab">
            <div class="form-group row">
                <div class="col-sm-3">
                <label for="fechaDefuncion_padre">Fecha de Defunción</label>
                <input type="date" class="form-control" id="fechaDefuncion_padre" name="fecha_defuncion_padre" value="{{ old('fecha_nacimiento_padre', $persona->fecha_defuncion) }}">
               </div>
             
                

        </div>
         
          
        </div>
    </div>

  




<script>
    $(document).ready(function () {
        // Selecciona y activa el tab Datos Generales al cargar la página
        $('#myTab a[href="#datosGeneralespadre"]').tab('show');
    });
</script>


<script>
    // Función para calcular la edad
    function calcularEdad(fechaNacimientopadre) {
        const fechaActual = new Date();
        const fechaNac = new Date(fechaNacimientopadre);
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
    document.getElementById('fecha_nacimiento_padre').addEventListener('change', function() {
        const fechaNacimiento = this.value;
        const edad = calcularEdad(fechaNacimiento);
        document.getElementById('anio_padre').value = edad !== undefined ? edad : '';  // Mostrar edad o vacío si no hay fecha válida
    });

    // Si el formulario ya tiene una fecha de nacimiento al cargar la página
    window.onload = function() {
        const fechaNacimiento = document.getElementById('fecha_nacimiento_padre').value;
        if (fechaNacimiento) {
            const edad = calcularEdad(fechaNacimiento);
            document.getElementById('anio_padre').value = edad;
            
            
          
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



