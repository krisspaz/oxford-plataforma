

        <!-- Dirección -->
      

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


       