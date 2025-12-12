<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection; // <-- opción si quieres exportar desde una colección
use Maatwebsite\Excel\Concerns\WithHeadings;

// <-- opción si prefieres usar una vista Blade

class PagosFiltradosExport implements FromCollection, WithHeadings
{
    protected $pagos;

    public function __construct($pagos)
    {
        $this->pagos = $pagos;
    }

    public function collection()
    {
        return $this->pagos->map(function ($pago) {
            return [
                'ID' => $pago->id,
                'Estudiante' => optional($pago->convenio)->inscripcion->estudiante->persona->nombres .' '.optional($pago->convenio)->inscripcion->estudiante->persona->apellidos ,
                'Inscripción' => optional($pago->convenio)->inscripcion->id,
                'Convenio' => optional($pago->convenio)->id,
                
                'Monto' => $pago->monto,
                'Tipo de Pago' => $pago->tipo_pago,
                'Exonerado' => $pago->exonerar ? 'Sí' : 'No',
                'Fecha de Pago' => $pago->fecha_pago,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Estudiante', 'Inscripción','Convenio','Monto', 'Tipo de Pago', 'Exonerado', 'Fecha de Pago'];
    }
}
