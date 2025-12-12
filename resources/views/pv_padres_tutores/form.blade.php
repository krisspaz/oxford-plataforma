<div class="form-group">
    <label for="codigofamiliar">Código Familiar:</label>
    @if(isset($padres_tutor))
        <!-- Vista Editar -->
        <input type="text" name="codigofamiliar" id="codigofamiliar" class="form-control" value="{{ $padres_tutor->codigofamiliar }}" disabled>
    @else
        <!-- Vista Crear -->
        <input type="text" name="codigofamiliar" id="codigofamiliar" class="form-control" value="Se generará automáticamente" disabled>
    @endif
</div>


<div class="form-group">
    <label for="padre_id">Padre:</label>
    <select name="padre_id" id="padre_id" class="form-control">
        <option value="">Seleccione un padre</option>
        @foreach($padres as $padre)
        <option value="{{ $padre->id }}" @if($padre->id == $padres_tutor->padre_id) selected @endif>{{ $padre->nombre }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="madre_id">Madre:</label>
    <select name="madre_id" id="madre_id" class="form-control">
        <option value="">Seleccione una madre</option>
        @foreach($madres as $madre)
        <option value="{{ $madre->id }}" @if($madre->id == $padres_tutor->madre_id) selected @endif>{{ $madre->nombre }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="encargado_id">Encargado:</label>
    <select name="encargado_id" id="encargado_id" class="form-control">
        <option value="">Seleccione un encargado</option>
        @foreach($encargados as $encargado)
        <option value="{{ $encargado->id }}" @if($encargado->id == $padres_tutor->encargado_id) selected @endif>{{ $encargado->nombre }}</option>
        @endforeach
    </select>
</div>