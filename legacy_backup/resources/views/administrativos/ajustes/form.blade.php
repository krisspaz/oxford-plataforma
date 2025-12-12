<div class="box box-default">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('error') }}
    </div>
@endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif
    <div class="box-header with-border">
        <h3 class="box-title">{{ $modo }} Administrativo</h3>
    </div>
    <div class="box-body">

        <div class="form-group">
            <label>Persona</label>
            <select name="persona_id" class="form-control select2" required>
                <option value="">Seleccione una persona</option>
                @foreach($personas as $persona)
                    <option value="{{ $persona->id }}" {{ (old('persona_id', $administrativo->persona_id ?? '') == $persona->id) ? 'selected' : '' }}>
                        {{ $persona->nombres }} {{ $persona->apellidos }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Cargo</label>
            <select name="cargo_id" class="form-control" required>
                <option value="">Seleccione un cargo</option>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id }}" {{ (old('cargo_id', $administrativo->cargo_id ?? '') == $cargo->id) ? 'selected' : '' }}>
                        {{ $cargo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select name="estado_id" class="form-control" required>
                <option value="">Seleccione un estado</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->id }}" {{ (old('estado_id', $administrativo->estado_id ?? '') == $estado->id) ? 'selected' : '' }}>
                        {{ $estado->estado }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Fotografía</label>
            @if(!empty($administrativo->fotografia_administrativo))
                <div>
                    <img src="{{ asset('storage/' . $administrativo->fotografia_administrativo) }}" width="100">
                </div>
            @endif
            <input type="file" name="fotografia_administrativo" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">{{ $modo }}</button>
        <a href="{{ route('ajuste-administrativos.index') }}" class="btn btn-default">Cancelar</a>
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
        allowClear: true,
        theme: 'bootstrap4'
    });
});
</script>
@endpush