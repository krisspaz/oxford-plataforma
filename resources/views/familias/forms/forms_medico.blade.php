<h3 class="text-center">INFORMACIÓN MEDICA</h3>
<br>

<input type="hidden" name="familia_id" value="{{ $familia->id }}">

<div class="mb-3">
    <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo</label>
    <select class="form-select" id="modalGrupoSanguineo" name="grupo_sanguineo">
        <option value="">Seleccione...</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
    </select>
</div>

<br>



<!-- Alergias -->
<div class="mb-3">
    <label for="alergias" class="form-label">Alergias</label>
    <textarea class="form-control" id="modalAlergias" name="alergias" rows="2" placeholder="Especificar alergias"></textarea>
</div>

<!-- Enfermedades -->
<div class="mb-3">
    <label for="enfermedades" class="form-label">Enfermedades</label>
    <textarea class="form-control" id="modalEnfermedades" name="enfermedades" rows="2" placeholder="Especificar Enfermedades"></textarea>
</div>

<div class="mb-3">
    <label for="enfermedades" class="form-label">Medicamentos</label>
    <textarea class="form-control" id="modalMedicamentos" name="Medicamentos" rows="2" placeholder="Especificar Medicamentos"></textarea>
</div>

<!-- Médico -->
<div class="mb-3">
    <label for="medico" class="form-label">Médico</label>
    <input type="text" class="form-control" id="modalDoctor" name="medico">
</div>

<!-- Teléfono Médico -->
<div class="mb-3">
    <label for="telefono_medico" class="form-label">Teléfono Médico</label>
    <input type="tel" class="form-control" id="modalTelefonoMedico" name="telefono_medico" placeholder="Teléfono del médico">
</div>

<!-- Observaciones -->
<div class="mb-3">
    <label for="observaciones" class="form-label">Observaciones</label>
    <textarea class="form-control" id="modalObservaciones" name="observaciones" rows="3" placeholder="Observaciones adicionales"></textarea>
</div>








