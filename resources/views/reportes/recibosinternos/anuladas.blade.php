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
        <h3 class="box-title">📄 Recibos Internos para Anulación</h3>
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


        {{-- Tabla de resultados --}}
        <div class="table-responsive">
            @php
                $agrupadas = $recibos->groupBy('numero');
            @endphp

            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="active">
                        <th>NIT</th>
                        <th>UUID</th>
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
                            $recibo = $grupo->first();
                            $pagoIds = $grupo->pluck('pago.id')->filter()->unique()->implode(', ');
                        @endphp
                        <tr>
                            <td>{{ $recibo->nit }}</td>
                            <td>{{ $recibo->guid }}</td>
                            <td>{{ $recibo->serie }}</td>
                            <td>{{ $recibo->numero }}</td>
                            <td>{{ $pagoIds ?: 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($recibo->created_at)->format('d/m/y') }}</td>
                            <td>
                                @if($recibo->link)
                                    <a href="{{ $recibo->link }}" target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-external-link"></i> Ver
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $recibo->motivo }}</td>
                            <td>
                                <a href="{{ route('reportes.recibos.show', $recibo->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> Detalle
                                </a>

                                <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#anularFacturaModal{{ $recibo->guid }}">
                                    <i class="fa fa-ban"></i> Anular
                                </button>
                                 <form action="{{ route('reportes.recibosinternos.anular', $numero) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="anular" value="0">
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i> Rechazar
                                        </button>
                                    </form>
                            </td>
                        </tr>

                        {{-- Modal de anulación por cada recibo --}}
                        <div class="modal fade" id="anularFacturaModal{{ $recibo->guid }}" tabindex="-1" role="dialog" aria-labelledby="anularFacturaModalLabel{{ $recibo->guid }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form method="POST" action="{{ route('recibointerno.procesar_anulacion', ['serie' => $recibo->serie]) }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Anular Recibo: {{ $recibo->numero }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="numero_serie" value="{{ $recibo->serie }}">
                                            <input type="hidden" name="numero_documento" value="{{ $recibo->guid }}">
                                            <input type="hidden" name="nit_receptor" value="{{ $recibo->nit ?? 'CF' }}">
                                            <input type="hidden" name="fecha_emision" value="{{ $recibo->created_at }}">

                                            <div class="form-group">
                                                <label for="motivo">Motivo de Anulación</label>
                                                <textarea name="motivo" class="form-control" required placeholder="Escribe el motivo de anulación...">{{ $recibo->motivo }}</textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Anular Documento</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron recibos internos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="text-center">
            {{ $recibos->links() }}
        </div>
    </div>
</div>
@endsection
