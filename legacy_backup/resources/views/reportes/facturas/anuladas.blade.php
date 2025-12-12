@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">

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
        <h3 class="box-title">📄 Facturas para Anulación </h3>
    </div>

      {{-- Selector de cantidad por página --}}
        <div class="form-group" style="margin-right: 15px; width: 70px;" >
            <label for="per_page">Mostrar:</label>
            <select name="per_page" class="form-control" onchange="this.form.submit()">
                @foreach([5, 10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ (request('per_page', $perPage) == $size) ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </div>

    <div class="box-body">
        {{-- Buscador por NIT --}}


        {{-- Tabla de resultados --}}
        <div class="table-responsive">
            @php
                $agrupadas = $facturas->groupBy('numero');
            @endphp

            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="active">
                        <th>NIT</th>
                        <th>DTE</th>
                        <th>Serie</th>
                        <th>Número</th>
                        <th>Pago ID(s)</th>
                        <th>Fecha de Emisión</th>
                        <th>Link</th>
                        <th>Motivo de Anulación</th>
                        <th style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agrupadas as $numero => $grupo)
                        @php
                            $factura = $grupo->first();
                            $pagoIds = $grupo->pluck('pago.id')->filter()->unique()->implode(', ');
                        @endphp
                        <tr>
                            <td>{{ $factura->nit }}</td>
                            <td>{{ $factura->guid }}</td>
                            <td>{{ $factura->serie }}</td>
                            <td>{{ $factura->numero }}</td>
                            <td>{{ $pagoIds ?: 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($factura->created_at)->format('d/m/y') }}</td>
                            <td>
                                @if($factura->link)
                                    <a href="{{ $factura->link }}" target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-external-link"></i> Ver
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $factura->motivo }}</td>
                            <td>
                                <a href="{{ route('reportes.facturas.show', $factura->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> Detalle
                                </a>

                               <!-- Botón que activa el modal -->
                            <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#anularFacturaModal{{ $factura->guid }}">
                                <i class="fa fa-ban"></i> Anular
                            </button>

                            <form action="{{ route('reportes.facturas.anular', $numero) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="anular" value="0">
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i> Rechazar
                                        </button>
                                    </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron facturas.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="text-center">
            {{ $facturas->links() }}
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="anularFacturaModal{{ $factura->guid }}" tabindex="-1" role="dialog" aria-labelledby="anularFacturaModalLabel{{ $factura->guid }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" id="form-anulacion" action="{{ route('anulador.enviar') }}">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="anularFacturaModalLabel{{ $factura->guid }}">Anular Factura: {{ $factura->numero }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="numero_serie" value="{{ $factura->serie }}">
            <input type="hidden" name="numero_documento" value="{{ $factura->guid }}">
            <input type="hidden" name="nit_receptor" value="{{ $factura->nit ?? 'CF' }}">
            <input type="hidden" name="fecha_emision" value="{{ $factura->created_at }}">

            <div class="form-group">
              <label for="motivo">Motivo de Anulación</label>
              <textarea name="motivo" class="form-control" required placeholder="Escribe el motivo de anulación...">{{$factura->motivo}}</textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="btn-anular">Anular Documento</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.getElementById('btn-anular').addEventListener('click', function (e) {
        Swal.fire({
            title: '¿Está seguro?',
            text: "¿Desea realmente anular el documento?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-anulacion').submit();
            }
        });
    });
</script>

@endsection
