<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boleta de Calificaciones</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 10px 0;
        }

        .info-estudiante {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        thead {
            display: table-header-group; /* HACE que se repita el encabezado en cada página */
        }

        .encabezado {
            text-align: center;
            font-size: 12px;
        }

        .logo {
            width: 80px;
        }

        .page-break {
            page-break-before: always;
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

@foreach($estudiantes as $index => $estudiante)

    <table>
        <thead>
            <tr>
                <td colspan="6" style="border: none; text-align: center;">
                      @if ($imagen_base64)
       <img src="data:image/{{ $tipo_imagen }};base64, {{ $imagen_base64 }}" class="logo" alt="Logo"><br>
    @else
       <div style="width: 100px; height: 100px; line-height: 100px; border: 1px dashed #ccc; margin: 0 auto; text-align: center;">[LOGO NO DISPONIBLE]</div>
    @endif

                    <strong>{{CRUDBooster::getSetting('nombre_del_establecimiento')}}</strong><br>
                    {{CRUDBooster::getSetting('direccion_del_establecimiento')}}<br>
                    Teléfono: {{CRUDBooster::getSetting('numero_de_telefono')}}<br><br>
                    <h2>Boleta de Calificaciones</h2>
                </td>
            </tr>

            <tr>
                <td colspan="6" style="text-align: left; border: none;">
                    <strong>Carné:</strong> {{ $estudiante->carnet }}<br>
                    <strong>Estudiante:</strong> {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}<br>
                    <strong>Grado:</strong> {{ $estudiante->cgshges->grados->nombre }}<br>
                    <strong>Curso:</strong> {{ $estudiante->cgshges->cursos->curso }}<br>
                    <strong>Jornada:</strong> {{ $estudiante->cgshges->jornadas->jornada->nombre }}<br>
                    <strong>Ciclo Escolar:</strong> {{ $ciclo_escolar }}
                </td>
            </tr>

            <tr>
                <th>Materia</th>
                <th>Bimestre I</th>
                <th>Bimestre II</th>
                <th>Bimestre III</th>
                <th>Bimestre IV</th>
                <th>Nota Final</th>
            </tr>
        </thead>

        <tbody>
            @foreach($notas[$estudiante->id] as $materia_id => $notasMateria)
                @php
                    $materiaNombre = $notasMateria->first()->materia->gestionMateria->nombre ?? 'Materia';
                    $bimestres = ['Bimestre I' => null, 'Bimestre II' => null, 'Bimestre III' => null, 'Bimestre IV' => null];
                      $bimestrespor = ['Bimestre I' => null, 'Bimestre II' => null, 'Bimestre III' => null, 'Bimestre IV' => null];

                        foreach ($notasMateria as $nota) {
                            if($tipo_promedio=="puntuacion"){

                                $bimestres[$nota->bimestres->nombre] = $nota->nota_acumulada;

                            }else  if($tipo_promedio=="porcentaje"){
                                 $bimestres[$nota->bimestres->nombre] = ($nota->nota_acumulada*$nota->bimestres->porcentaje)/$nota->bimestres->punteo_maximo;

                            }else{
                                  $bimestres[$nota->bimestres->nombre] = $nota->nota_acumulada;
                                 $bimestrespor[$nota->bimestres->nombre] = ($nota->nota_acumulada*$nota->bimestres->porcentaje)/$nota->bimestres->punteo_maximo;
                            }
                        }

                       if($tipo_promedio=="puntuacion"){
                             $promedio = collect($bimestres)->map(function ($item) {
                            return $item ?? 0; // Si es null, usar 0
                        })->sum() / 4;

                        }else if($tipo_promedio=="porcentaje"){
                                $promedio = collect($bimestres)->map(function ($item) {
                            return $item ?? 0; // Si es null, usar 0
                        })->sum() ;
                        }else{
                             $promedio = collect($bimestrespor)->map(function ($item) {
                            return $item ?? 0; // Si es null, usar 0
                        })->sum() ;
                        }
                @endphp
                <tr>
                    <td>{{ $materiaNombre }}</td>
                   <td>
                        {{ $bimestres['Bimestre I'] ?? '0' }}
                        @if($tipo_promedio != 'puntuacion' && $tipo_promedio != 'mixto')
                            %
                        @endif
                    </td>
                        <td>
                            {{ $bimestres['Bimestre II'] ?? '0' }}
                            @if($tipo_promedio != 'puntuacion' && $tipo_promedio != 'mixto')
                                %
                            @endif
                        </td>
                       <td>
                            {{ $bimestres['Bimestre III'] ?? '0' }}
                            @if($tipo_promedio != 'puntuacion' && $tipo_promedio != 'mixto')
                                %
                            @endif
                        </td>
                      <td>
                            {{ $bimestres['Bimestre IV'] ?? '0' }}
                            @if($tipo_promedio != 'puntuacion' && $tipo_promedio != 'mixto')
                                %
                            @endif
                        </td>
                        <td>
                            {{ number_format($promedio, 2) }}
                            @if($tipo_promedio != 'puntuacion')
                                %
                            @endif
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>
