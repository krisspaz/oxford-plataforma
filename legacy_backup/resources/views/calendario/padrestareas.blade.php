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

    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.17/index.global.min.js'></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilo para agrandar el modal -->
    <style>
        .swal-wide {
            width: 600px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            if (typeof FullCalendar === 'undefined') {
                console.error('FullCalendar no está definido.');
                return;
            }

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
                events: function(fetchInfo, successCallback, failureCallback) {
    fetch("{{ route('eventos.tareaspadres') }}")
        .then(response => response.json())
        .then(data => {
            console.log("EVENTOS RECIBIDOS:", data);
            successCallback(data);
        })
        .catch(error => {
            console.error("Error cargando eventos:", error);
            failureCallback(error);
        });
},
eventDidMount: function(info) {
    // Este busca "Materia:" y lo pone en negrita y otro color
    const el = info.el.querySelector('.fc-event-title');

    if (el) {
        el.innerHTML = el.innerHTML.replace(/Materia:(.*?)Grado:/, function(_, materia) {
    return `<span style="color:#200c24; font-weight:bold;">Materia:${materia}</span>Grado:`;
});
    }
}

,
                eventClick: function (info) {
    let tareas = info.event.extendedProps && info.event.extendedProps.tareas 
        ? info.event.extendedProps.tareas 
        : 'No hay tareas registradas.';

    Swal.fire({
        title: `Tareas de ${info.event.title}`,
        icon: 'info',
        html: `<div style="text-align:left; font-size:14px;">${tareas}</div>`,
        confirmButtonText: 'Cerrar'
    });

    // No seguir a la URL predeterminada
    info.jsEvent.preventDefault();
}
            });

            calendar.render();
        });

        
    </script>



@endsection
