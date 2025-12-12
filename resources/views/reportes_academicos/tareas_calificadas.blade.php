<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas Calificadas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        .grupo { background-color: #f0f0f0; font-weight: bold; }
        .encabezados { background-color: #e0e0e0; font-weight: bold; }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
        }
        .header .titulo {
            font-size: 22px;
            font-weight: bold;
        } .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #888;
        }
    </style>
</head>

<div class="header">
    <img src="{{ public_path('images/logo.png') }}" alt="Logo">
    <p class="titulo">OXFORD</p>
    <p>Dirección: 2a. Calle 16-94 Zona 4 Cobán, Alta Verapaz</p>
    <p>Teléfono: (502) 7951 3898</p>
</div>
<body>
    <h2>Tareas Calificadas - Ciclo Escolar {{ $anio_ciclo_escolar }}</h2>

    <p><strong>Bimestre:</strong> {{ $tareasCalificadas->first()->tarea->bimestre->nombre }}</p>
    <p><strong>Estudiante:</strong> {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</p>
    <p><strong>Grado:</strong> {{ $estudiante->asignacion->grados->nombre }}</p>
    <p><strong>Curso:</strong> {{ $estudiante->asignacion->cursos->curso }}</p>
    <p><strong>Jornada:</strong> {{ $estudiante->asignacion->jornadas->jornada->nombre }}</p>

    @php
        $agrupadas = $tareasCalificadas->groupBy(function($item) {
            return $item->tarea->materia->gestionMateria->nombre . ' - Docente: ' .
                   $item->tarea->docente->persona->nombres . ' ' .
                   $item->tarea->docente->persona->apellidos;
        });
    @endphp

    <table>
        @forelse($agrupadas as $grupo => $tareas)
            <tr><td colspan="4" class="grupo">{{ $grupo }}</td></tr>
            <tr class="encabezados">
                <th>Tarea</th>
                <th>Calificación</th>
                <th>Comentario</th>
                <th>Fecha</th>
            </tr>
            @foreach($tareas as $tareaEstudiante)
                <tr>
                    <td>{{ $tareaEstudiante->tarea->titulo }}</td>
                    <td>
                        @if($tareaEstudiante->calificacion->calificacion === null)
                            Sin Calificar
                        @else
                            {{ $tareaEstudiante->calificacion->calificacion }}
                        @endif
                    </td>
                    <td>{{ $tareaEstudiante->calificacion->comentarios }}</td>
                    <td>{{ $tareaEstudiante->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="4">No hay tareas calificadas.</td>
            </tr>
        @endforelse
    </table>
    <div class="footer">
        <p>Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
</body>
</html>
