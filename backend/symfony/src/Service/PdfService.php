<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\Enrollment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private Environment $twig;
    private string $publicDir;

    public function __construct(Environment $twig, string $publicDir = '/var/www/symfony/public')
    {
        $this->twig = $twig;
        $this->publicDir = $publicDir;
    }

    private function createDompdf(): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        return new Dompdf($options);
    }

    public function generateCarnet(Student $student, Enrollment $enrollment): string
    {
        $html = $this->twig->render('pdf/carnet.html.twig', [
            'student' => $student,
            'enrollment' => $enrollment,
            'year' => date('Y'),
        ]);

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 243, 153], 'landscape'); // Credit card size
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateBoleta(Student $student, array $grades, string $bimester): string
    {
        $html = $this->twig->render('pdf/boleta.html.twig', [
            'student' => $student,
            'grades' => $grades,
            'bimester' => $bimester,
            'date' => new \DateTime(),
        ]);

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateEstadoCuenta(Student $student, array $quotas, array $payments): string
    {
        $html = $this->twig->render('pdf/estado_cuenta.html.twig', [
            'student' => $student,
            'quotas' => $quotas,
            'payments' => $payments,
            'date' => new \DateTime(),
        ]);

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateCorteDia(array $data, \DateTimeInterface $date): string
    {
        $html = $this->twig->render('pdf/corte_dia.html.twig', [
            'data' => $data,
            'date' => $date,
        ]);

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateCuadroFinal(array $students, string $grade, string $cycle): string
    {
        $html = $this->twig->render('pdf/cuadro_final.html.twig', [
            'students' => $students,
            'grade' => $grade,
            'cycle' => $cycle,
            'date' => new \DateTime(),
        ]);

        $dompdf = $this->createDompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
