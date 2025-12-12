@extends('crudbooster::admin_template')

@section('content')

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            Exonerar Cuotas Pendientes —
            {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
        </h3>
    </div>

    <div class="box-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($cuotas->count() == 0)
            <div class="alert alert-info">No hay cuotas pendientes para este estudiante.</div>
        @else

        <form id="formExonerar" method="POST" action="{{ route('cuotas.exonerar.solicitar') }}">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll"> Seleccionar Todos
                        </th>
                        <th>Producto</th>
                        <th>Mes</th>
                        <th>Monto</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuotas as $cuota)
                        <tr>
                            <td>
                                <input type="checkbox" class="check-cuota" name="cuotas[]" value="{{ $cuota->id }}">
                            </td>

                            {{-- PRODUCTO USANDO detalle->nombre --}}
                            <td>
                                {{ optional(optional($cuota->productoSeleccionado)->detalle)->nombre ?? 'Sin producto' }}
                            </td>

                            <td>{{ $cuota->fecha_vencimiento->translatedFormat('F Y') }}</td>
                            <td>Q{{ number_format($cuota->monto_cuota, 2) }}</td>
                            <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <label>Motivo de exoneración</label>
                <textarea name="comentario" class="form-control" required></textarea>
            </div>

            <button type="button" class="btn btn-danger" onclick="confirmarExoneracion()">
                <i class="fa fa-ban"></i> Exonerar Cuotas
            </button>
        </form>

        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Seleccionar / deseleccionar todos
document.getElementById('selectAll').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.check-cuota');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Confirmación SweetAlert
function confirmarExoneracion() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esto marcará las cuotas como BAJA VOLUNTARIA.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, exonerar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formExonerar').submit();
        }
    });
}
</script>

@endsection
