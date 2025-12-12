@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1>Datos Médicos</h1>

<div class="row mt-3">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="grupo_sanguineo">Grupo Sanguíneo</label>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="form-control">
                        <option value="">Seleccionar</option>
                        <option value="A+" {{ old('grupo_sanguineo') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('grupo_sanguineo') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('grupo_sanguineo') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('grupo_sanguineo') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('grupo_sanguineo') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('grupo_sanguineo') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('grupo_sanguineo') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('grupo_sanguineo') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="alergias">Alergias</label>
                    <textarea name="alergias" id="alergias" rows="3" class="form-control" placeholder="Describa todas las alergias">{{ old('alergias') }}</textarea>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="enfermedades">Enfermedades</label>
                    <textarea name="enfermedades" id="enfermedades" rows="3" class="form-control" placeholder="Describa aquí enfermedades que padezca el Estudiante">{{ old('enfermedades') }}</textarea>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="medicamentos">Medicamentos</label>
                    <textarea name="medicamentos" id="medicamentos" rows="3" class="form-control" placeholder="Describa aquí los medicamentos ingeridos por el Estudiante">{{ old('medicamentos') }}</textarea>
                </div>
            </div>
        </div> <!-- Fin de fila (row) -->

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="medico">Nombre del Médico de Cabecera</label>
                    <input type="text" name="medico" class="form-control" value="{{ old('medico') }}">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="telefono_medico">Teléfono del Médico</label>
                    <input type="text" name="telefono_medico" class="form-control" value="{{ old('telefono_medico') }}">
                </div>
            </div>
        </div> <!-- Fin de fila -->

        <div class="row">
            <div class="col-md-9">
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Escriba aquí cualquier observación importante">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div> <!-- Fin de fila -->
    </div> <!-- Fin de columna md-9 -->
</div> <!-- Fin de row -->
