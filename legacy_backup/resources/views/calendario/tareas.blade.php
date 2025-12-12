@extends('crudbooster::admin_template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📅 Calendario de Tareas</h3>
    </div>
    <div class="card-body">
        <div class="box-body">
            <div id="calendar" style="min-height: 600px; border: 1px solid #ccc;"></div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.17/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'local',
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'multiMonthYear',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            prev: 'Anterior',
            next: 'Siguiente',
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        events: "{{ route('eventos.tareas') }}",
        eventClick: function (info) {
            const ep = info.event.extendedProps || {};
            const tareaId = ep.tarea_id || null;
            const tareaEstudianteId = ep.tarea_estudiante_id || null;
            const descripcion = ep.tareas || 'No hay tareas registradas.';
            const descripcion2 = ep.descripcion || 'No hay tareas registradas.';
            const estado = ep.estado || 'pendiente';
            const fechaExpiracion = ep.fexpiracion || null;
            const archivo = ep.archivo || null;

            const esEstudiante = !!tareaEstudianteId;

            // Validación de fecha usando moment y formato YYYY-MM-DD

                const fechaVigente = fechaExpiracion &&
    moment(fechaExpiracion, 'YYYY-MM-DD').isSameOrAfter(moment().startOf('day'));


            let contenidoModal = `<div style="text-align:left; font-size:14px;">
                <p><strong>Estado:</strong> ${estado.charAt(0).toUpperCase() + estado.slice(1)}</p>
                <p style="white-space:pre-wrap;">${descripcion}</p>
                 <p style="white-space:pre-wrap;">${descripcion2}</p>
                <hr>`;

            if (esEstudiante) {
                // Subir archivo si está pendiente y fecha vigente
                if (estado === 'pendiente' && fechaVigente) {
                    contenidoModal += `
                        <form id="formSubirTarea" enctype="multipart/form-data">
                            <input type="file" name="archivo" class="form-control" required>
                        </form>`;
                }

                 if (estado === 'entregada' && fechaVigente) {
                // Botones de descargar y eliminar si hay archivo
                if (archivo) {
                    contenidoModal += `<div style="margin-top:10px; display:flex; gap:8px; flex-wrap: wrap;">`;
                    contenidoModal += `<a href="/admin/estudiantes/tareas/${tareaEstudianteId}/descargar" class="btn btn-sm btn-success" target="_blank">
                        <i class="fa fa-download"></i> Descargar
                    </a>`;

                    // Permitir eliminar si la fecha no venció
                    if (fechaVigente) {
                      // Dentro de tu eventClick de FullCalendar
const eliminarUrl = "{{ route('estudiantes.tareas.eliminarArchivo', ':id') }}".replace(':id', tareaEstudianteId);

contenidoModal += `
                <form action="${eliminarUrl}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar el archivo?');">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i> Eliminar
                    </button>
                </form>
                `;
                    }

                    contenidoModal += `</div>`;
                }

                }

                // Si está pendiente pero fecha vencida
                if (estado === 'pendiente' && !fechaVigente) {
                    contenidoModal += `<span class="badge badge-danger mt-2">Fecha límite vencida</span>`;
                }

                // Si no hay acción disponible
                if (!archivo && estado !== 'pendiente') {

                    contenidoModal += `<div style="margin-top:10px; display:flex; gap:8px; flex-wrap: wrap;">`;
                    contenidoModal += `<a href="/admin/estudiantes/tareas/${tareaEstudianteId}/descargar" class="btn btn-sm btn-success" target="_blank">
                        <i class="fa fa-download"></i> Descargar
                    </a>`;

                    // Permitir eliminar si la fecha no venció
                    if (fechaVigente) {


                        contenidoModal += `<form action="/admin/tarea-estudiante/${tareaEstudianteId}/eliminar-archivo" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar el archivo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>`;
                    }

                    contenidoModal += `</div>`;

                }
            }

            contenidoModal += `</div>`;

            Swal.fire({
                title: `Tarea: ${info.event.title}`,
                icon: 'info',
                html: contenidoModal,
                showCancelButton: true,
                confirmButtonText: esEstudiante && estado === 'pendiente' && fechaVigente ? 'Subir Archivo' : null,
                cancelButtonText: 'Cerrar',
                width: '600px',
                didOpen: () => {
                    if (!esEstudiante || estado !== 'pendiente' || !fechaVigente) {
                        const btn = Swal.getConfirmButton();
                        if (btn) btn.style.display = 'none';
                    }
                },
                preConfirm: () => {
                    if (!esEstudiante || estado !== 'pendiente' || !fechaVigente) return false;

                    const formEl = document.getElementById('formSubirTarea');
                    if (!formEl) return;

                    const formData = new FormData(formEl);

                    return fetch(`/estudiantes/tareas/${tareaEstudianteId}/subir`, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error al subir');
                        return response.text();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Error: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && esEstudiante && estado === 'pendiente' && fechaVigente) {
                    Swal.fire('¡Éxito!', 'Archivo subido correctamente.', 'success')
                        .then(() => calendar.refetchEvents());
                } else if (!esEstudiante && tareaId) {
                    // Docentes abren show de la tarea
                    window.open(`/docentes/tareas/${tareaId}`, '_blank');
                }
            });

            info.jsEvent.preventDefault();
        }
    });

    calendar.render();
});
</script>
@endsection
