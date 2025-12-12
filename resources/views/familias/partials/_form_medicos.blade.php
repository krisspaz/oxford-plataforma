<h1>Datos Médicos</h1>

<div class="row mt-3">
    <!-- Columna para la fotografía (izquierda) -->
    
    <!-- Columna para el resto de los campos (derecha) -->
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="grupo_sanguineo">Grupo Sanguíneo</label>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="form-control">
                        <option value="Seleccionar">Seleccionar</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="alergias">Alergias</label>
                    <textarea name="alergias" id="alergias" rows="3" class="form-control" placeholder="Describa todas las alergias" ></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="enfermedades">Enfermedades</label>
                    <textarea name="enfermedades" id="enfermedades" rows="3" class="form-control" placeholder="Describa aquí enfermedades que padezca el Estudiante" ></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="medicamentos">Medicamentos</label>
                    <textarea name="medicamentos" id="medicamentos" rows="3" class="form-control" placeholder="Describa aquí los medicamentos ingeridos por el Estudiante" ></textarea>
                </div>
            </div>
        </div> <!-- Fin de fila (row) -->

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="medico">Nombre del Médico de Cabecera</label>
                    <input type="text" name="medico" class="form-control" >
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="telefono_medico">Teléfono del Médico</label>
                    <input type="text" name="telefono_medico" class="form-control" >
                </div>
            </div>
        </div> <!-- Fin de fila -->

        <div class="row">
            <div class="col-md-9">
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Escriba aquí cualquier observación importante" ></textarea>
                </div>
            </div>
        </div> <!-- Fin de fila -->
    </div> <!-- Fin de columna md-9 -->
</div> <!-- Fin de row -->
