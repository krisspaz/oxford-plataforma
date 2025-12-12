<div class="mb-3">
    <label for="name" class="form-label">Nombre</label>
    <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label for="email" class="form-label">Correo Electrónico</label>
    <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" class="form-control" required>
</div>

@if(!isset($usuario))
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
@else
    <div class="mb-3">
        <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
@endif

@php
$noPermitidos = ['SECRETARIA', 'ADMINISTRATIVO', 'Super Administrator', 'COORDINACION ACADEMICA'];
@endphp

<div class="mb-3">
    <label for="id_cms_privileges" class="form-label">Privilegio</label>
    <select name="id_cms_privileges" class="form-control" required>
        <option value="">Seleccione una opción</option>
        @foreach($privilegios as $privilegio)
        @if (!in_array($privilegio->name, $noPermitidos))
            <option value="{{ $privilegio->id }}" {{ (old('id_cms_privileges', $usuario->id_cms_privileges ?? '') == $privilegio->id) ? 'selected' : '' }}>
                {{ $privilegio->name }}
            </option>
            @endif
        @endforeach
    </select>
  
    <div class="mb-3">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#infoPrivilegios">
            <i class="fa fa-info-circle"></i> Acerca de los Privilegios
        </button>
    </div>

</div>

<div class="mb-3">
    <label for="photo" class="form-label">Foto (opcional)</label>
    <input type="file" name="photo" class="form-control">
    @if(isset($usuario) && $usuario->photo)
        <img src="{{ asset('storage/' . $usuario->photo) }}" width="100" class="mt-2">
    @endif
</div>


<div class="form-group">
    <label>Asociar a Persona</label>
    <select name="persona_id" id="persona_id"  class="form-control select2" required>
        <option value="">Seleccione</option>
        @foreach($personas as $p)
 
        <option value="{{ $p->id }}" {{ old('persona_id', $usuario->id ?? '') == $p->cms_users_id ? 'selected' : '' }}>
               {{ $p->nombres }} {{ $p->apellidos }}
            </option>
        @endforeach
    </select>
</div>


<!-- Modal -->
<div class="modal fade" id="infoPrivilegios" tabindex="-1" role="dialog" aria-labelledby="infoPrivilegiosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="infoPrivilegiosLabel">Información sobre los Privilegios</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Los privilegios como <strong>Docentes</strong> o para el <strong>Personal Administrativo</strong> se crean desde los módulos correspondientes del sistema. Este formulario solo se utiliza para asignar privilegios a Usuarios No Administrativos.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

@push('bottom')
<!-- Select2 CSS & JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4', // Opcional, puedes quitar si no usas Bootstrap 4
        placeholder: 'Seleccione',
        allowClear: true
    });
});
</script>
@endpush