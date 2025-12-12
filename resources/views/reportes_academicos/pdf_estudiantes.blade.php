<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Estudiantes - {{ $cicloEscolar }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
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
        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #0c0d0e;
            padding: 8px;
            text-align: left;
            white-space: nowrap; /* Evita saltos de línea */
        }
        table th {
            background-color: #1fb0e9;
            color: #080303;
            text-transform: uppercase;
        }
        table td:nth-child(2), table td:nth-child(3) {
            width: auto; /* Ancho automático para apellidos y nombres */
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>

     <?php
        // --- Bloque PHP para cargar el Logo en Base64 desde Storage ---

        // 1. OBTENER LA RUTA RELATIVA: "uploads/2025-10/..."
        $ruta_relativa_bd = CRUDBooster::getSetting('logo_reporte');

        // 2. CONSTRUIR LA RUTA ABSOLUTA USANDO storage_path()
        // Esto apunta directamente a: /storage/app/uploads/2025-10/archivo.png
        $ruta_logo_completa = storage_path('app/' . $ruta_relativa_bd);

        $imagen_base64 = null;
        $tipo_imagen = 'png';

        // 3. VERIFICAR EXISTENCIA y GENERAR BASE64
        if (!empty($ruta_relativa_bd) && file_exists($ruta_logo_completa) && !is_dir($ruta_logo_completa)) {
            try {
                $tipo_imagen = pathinfo($ruta_logo_completa, PATHINFO_EXTENSION);
                $datos_imagen = file_get_contents($ruta_logo_completa);
                $imagen_base64 = base64_encode($datos_imagen);
            } catch (\Exception $e) {
                // Si falla la lectura, $imagen_base64 sigue siendo null.
            }
        }
    ?>

    <div class="header">
    @if ($imagen_base64)
       <img src="data:image/{{ $tipo_imagen }};base64, {{ $imagen_base64 }}" alt="Logo del Reporte">
    @else
       <div style="width: 100px; height: 100px; line-height: 100px; border: 1px dashed #ccc; margin: 0 auto; text-align: center;">[LOGO NO DISPONIBLE]</div>
    @endif

    <div class="school-name">{{CRUDBooster::getSetting('nombre_del_establecimiento')}}</div>
    <div>{{CRUDBooster::getSetting('direccion_del_establecimiento')}}</div>
    <div>Teléfono: {{CRUDBooster::getSetting('numero_de_telefono')}}</div>
</div>

    <h2>Listado de Estudiantes - Ciclo Escolar {{ $cicloEscolar }}</h2>

    @php
        // Ordenamos los estudiantes por apellido
        $estudiantesOrdenados = $estudiantes->sortBy(function($matricula) {
            return $matricula->estudiante->persona->apellidos ?? '';
        });
        // Inicializamos el contador para la numeración
        $contador = 1;
    @endphp

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Carné</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Ciclo Escolar</th>
                <th>Grado</th>
                <th>Curso</th>
                <th>Sección</th>
                <th>Jornada</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estudiantesOrdenados as $matricula)
                <tr>
                    <td>{{ $contador++ }}</td> <!-- Contador manual para numerar -->
                    <td>{{ $matricula->estudiante->carnet ?? 'No definido' }}</td>
                    <td>{{ $matricula->estudiante->persona->apellidos ?? 'No definido' }}</td>
                    <td>{{ $matricula->estudiante->persona->nombres ?? 'No definido'}}</td>
                    <td>{{ $matricula->ciclo_escolar }}</td>
                    <td>{{ $matricula->cgshges->grados->nombre ?? 'No definido' }}</td>
                    <td>{{ $matricula->cgshges->cursos->curso ?? 'No definido' }}</td>
                    <td>{{ $matricula->cgshges->secciones->seccion ?? 'No definido' }}</td>
                    <td>{{ $matricula->cgshges->jornadas->jornada->nombre ?? 'No definido' }}</td>
                    <td>{{ $matricula->estado->estado ?? 'No definido' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
