
  <!-- Dirección -->

    <br>

        <div class="form-group row">
            <div class="col-sm-3">
                <label for="{{ $prefix }}[pais]">País (*):</label>
                <input type="text" class="form-control" id="{{ $prefix }}[pais]" name="{{ $prefix }}[pais]" value="{{ old($prefix.'.pais') }}">
             </div>
             <div class="col-sm-3">

                <label for="{{ $prefix }}[departamento_id]">Departamento:</label>
            <select name="{{ $prefix }}[departamento_id]" id="{{ $prefix }}[departamento_id]" class="form-control">
                <option value="">Seleccione un departamento</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ old($prefix.'.departamento_id', $persona->municipio->departamento_id) == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                @endforeach
            </select>
            @error('{{ $prefix }}[departamento_id]')
                <div class="text-danger">{{ $message }}</div>
            @enderror


             </div>

              <div class="col-sm-3">
                <label for="{{ $prefix }}[municipio_id]">Municipio:</label>
                <select name="{{ $prefix }}[municipio_id]" id="{{ $prefix }}[municipio_id]" class="form-control">
                    <option value="">Seleccione un municipio</option>
                </select>
                @error('{{ $prefix }}[municipio_id]')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

              </div>


              <div class="col-sm-2">

                <label for="{{ $prefix }}[codigoPostal]">Código Postal</label>
                <input type="text" class="form-control" id="{{ $prefix }}[codigoPostal]" name="{{ $prefix }}[codigoPostal]" readonly>

                </div>

        </div>
        
        <div class="form-group row">
            <div class="col-sm-5">
                <label for="{{ $prefix }}[direccion]">Dirección (*):</label>
   
                <textarea class="form-control" id="{{ $prefix }}[direccion]" name="{{ $prefix }}[direccion]" rows="6" required>{{ old($prefix.'.direccion', $persona->direccion) }}</textarea>
            </div>

            <div class="col-sm-5">
                <label for="{{ $prefix }}[email]">Correo Electrónico (*):</label>
                <input type="email" class="form-control" id="{{ $prefix }}[email]" name="{{ $prefix }}[email]" value="{{ old($prefix.'.email',$persona->email) }}" required>
            </div>

            <div class="col-sm-3">
                <label for="{{ $prefix }}[telefono_casa]">Teléfono Casa:</label>
                <input type="text" class="form-control" id="{{ $prefix }}[telefono_casa]" name="{{ $prefix }}[telefono_casa]" value="{{ old($prefix.'.telefono_casa',$persona->telefono_casa) }}">
            </div>

            <div class="col-sm-3">
                <label for="{{ $prefix }}[telefono_mobil]">Teléfono Móvil (*):</label>
                 <input type="text" class="form-control" id="{{ $prefix }}[telefono_mobil]" name="{{ $prefix }}[telefono_mobil]" value="{{ old($prefix.'.telefono_mobil',$persona->telefono_mobil) }}"required>
            </div>
        </div>
       



