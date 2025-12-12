@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Boleta de Calificaciones</h3>
    </div>
    <div class="box-body">
        <h4>Estudiante: {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</h4>
        <table class="table table-bordered">
            <thead>
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
                @foreach($notas as $materia_id => $notasMateria)
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

        <input type="hidden" name="ciclo_escolar" value="{{ $ciclo_escolar }}">

    <a href="{{ route('boleta.pdf', ['estudiante_id' => $estudiante->id, 'tipo_promedio' => $tipo_promedio, 'ciclo_escolar' => $ciclo_escolar]) }}"
   class="btn btn-sm btn-danger"
   target="_blank">
    Descargar PDF
</a>
    </div>
</div>
@endsection
