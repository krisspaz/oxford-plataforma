@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">

    {{-- SweetAlert --}}
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
            html: `<pre style="text-align:left;white-space:pre-wrap;">{!! nl2br(e(session('error'))) !!}</pre>`,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif

    <div class="box-header with-border">
        <h3 class="box-title">📄 Reporte de Recibos SAT Emitidos</h3>
    </div>

    <div class="box-body">
        {{-- Buscador por NIT --}}
        <form method="POST" action="{{ route('reportes.recibos.buscar') }}" class="form-inline mb-3">
            @csrf
            <div class="form-group">
                <label for="nit" class="control-label mr-2">Buscar por NIT:</label>
                <input type="text" name="nit" id="nit" class="form-control" placeholder="Ej. 12345678">
            </div>
            <button type="submit" class="btn btn-primary ml-2"><i class="fa fa-search"></i> Buscar</button>
            <a href="{{ route('reportes.recibos.index') }}" class="btn btn-default ml-2"><i class="fa fa-refresh"></i> Limpiar</a>
        </form>

        @php
            $activos = collect($recibos)->map(function($r){
                $r->origen = 'emitido';
                $r->anulada_en = $r->anulada_en ?? null;
                $r->anular = $r->anular ?? 0;
                $r->cms_users_id = $r->cms_users_id ?? null;
                return $r;
            });
            $anulados = collect($recibosanulados)->map(function($r){
                $r->origen = 'anulado';
                return $r;
            });
            $combined = $activos->merge($anulados);
            $agrupadas = $combined->groupBy('numero');
        @endphp

        <div class="table-responsive">
            <table id="recibosTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr class="active">
                        <th>NIT</th>
                        <th>Serie</th>
                        <th>Número</th>
                        <th>Pago ID(s)</th>
                        <th>Fecha de Emisión</th>
                        <th>Estado</th>
                        <th>Anulado por</th>
                        <th>Fecha anulación</th>
                        <th>Link</th>
                        <th style="width: 260px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agrupadas as $numero => $grupo)
                        @php
                            $item = $grupo->first();
                            $isAnulado = $grupo->contains(function($v) {
                                return (!empty($v->anulada_en) || ($v->origen ?? '') == 'anulado');
                            });
                            $hasSolicitud = $grupo->contains(function($v){
                                return ($v->anular ?? 0) == 1 && empty($v->anulada_en);
                            });
                            $pagoIds = $grupo->pluck('pago')->filter()->map(function($p){
                                return data_get($p,'id');
                            })->filter()->unique()->implode(', ');
                            $fechaEmision = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : 'N/A';
                            $registroAnulado = $grupo->first(function($v){
                                return (!empty($v->anulada_en) || ($v->origen ?? '') == 'anulado' || ($v->anular ?? 0) == 1);
                            });
                            $anulado_por = $registroAnulado && !empty($registroAnulado->cmsUser) ? ($registroAnulado->cmsUser->name ?? $registroAnulado->cmsUser->username ?? 'Usuario') : null;
                            $anulada_en = $registroAnulado && !empty($registroAnulado->anulada_en) ? \Carbon\Carbon::parse($registroAnulado->anulada_en)->format('d/m/Y H:i') : null;
                            $link = $item->link ?? null;
                            if($link && !preg_match('/^https?:\\/\\//', $link)){
                                $link = asset('storage/' . ltrim($link, '/'));
                            }
                        @endphp
                        <tr
                            @if($isAnulado)
                                style="background:#fff6f6;"
                            @elseif($hasSolicitud)
                                style="background:#fff8e1;"
                            @endif
                        >
                            <td>{{ $item->nit ?? 'N/A' }}</td>
                            <td>{{ $item->serie ?? 'N/A' }}</td>
                            <td>{{ $numero }}</td>
                            <td>{{ $pagoIds ?: 'N/A' }}</td>
                            <td>{{ $fechaEmision }}</td>
                            <td>
                                @if($hasSolicitud)
                                    <span class="badge" style="background:#f0ad4e;color:#fff;">Solicitud de Anulación</span>
                                @elseif($isAnulado)
                                    <span class="badge" style="background:#d9534f;color:#fff;">Anulado</span>
                                @else
                                    <span class="badge" style="background:#5cb85c;color:#fff;">Emitido</span>
                                @endif
                            </td>
                            <td>{{ $anulado_por ?? '-' }}</td>
                            <td>{{ $anulada_en ?? '-' }}</td>
                            <td>
                                @if($link)
                                    <a href="{{ $link }}" target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-external-link"></i> Ver
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('reportes.recibos.show', $item->serie) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> Detalle
                                </a>
                                <a href="{{ route('reportes.recibos.descargarPDF', $item->serie) }}" class="btn btn-xs btn-success">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </a>

                                {{-- Emitidos sin solicitud --}}
                                @if(!$isAnulado && !$hasSolicitud)
                                    <button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#anularModal{{ $numero }}">
                                        <i class="fa fa-ban"></i> Anular
                                    </button>
                                @endif

                                {{-- Solicitud de Anulación pendiente: Rechazar --}}
                                @if($hasSolicitud)
                                    <form action="{{ route('reportes.recibos.anular', $numero) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="anular" value="0">
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i> Rechazar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No hay registros disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modales de Solicitar Anulación --}}
@foreach($agrupadas as $numero => $grupo)
    @php
        $item = $grupo->first();
        $isAnulado = $grupo->contains(function($v) { return (!empty($v->anulada_en) || ($v->origen ?? '') == 'anulado'); });
        $hasSolicitud = $grupo->contains(function($v){ return ($v->anular ?? 0) == 1 && empty($v->anulada_en); });
    @endphp

    @if(!$isAnulado && !$hasSolicitud)
    <div class="modal fade" id="anularModal{{ $numero }}" tabindex="-1" role="dialog" aria-labelledby="anularModalLabel{{ $numero }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('reportes.recibos.anular', $numero) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="anularModalLabel{{ $numero }}">Solicitud de Anulación - Nº {{ $numero }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>¿Desea anular este Recibo?</label><br>
                            <label class="mr-2"><input type="radio" name="anular" value="1" required> Sí</label>
                            <label><input type="radio" name="anular" value="2" required> No</label>
                        </div>
                        <div class="form-group">
                            <label for="motivo">Motivo de la anulación</label>
                            <textarea name="motivo" class="form-control" rows="3" required></textarea>
                        </div>

                        <input type="hidden" name="numero_documento" value="{{ $numero }}">
                        <input type="hidden" name="serie" value="{{ $item->serie ?? '' }}">
                        <input type="hidden" name="fecha_emision" value="{{ $item->created_at ?? '' }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

{{-- DataTables CSS/JS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#recibosTable').DataTable({
        "searching": true,
            "ordering": true,
             lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
    pageLength: 100,
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sPrevious": "Anterior",
                    "sNext":     "Siguiente",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
    responsive: true
});
});
</script>

@endsection
