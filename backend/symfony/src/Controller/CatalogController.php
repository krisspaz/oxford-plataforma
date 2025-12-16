<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalogs')]
class CatalogController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    #[Route('/grades', methods: ['GET'])]
    public function grades(): JsonResponse
    {
        // TODO: Get from Grade entity
        $grades = [
            ['id' => 1, 'level' => 'Preprimaria', 'name' => 'Kinder 4', 'sections' => ['A', 'B']],
            ['id' => 2, 'level' => 'Preprimaria', 'name' => 'Kinder 5', 'sections' => ['A', 'B']],
            ['id' => 3, 'level' => 'Primaria', 'name' => '1ro Primaria', 'sections' => ['A', 'B']],
            ['id' => 4, 'level' => 'Básico', 'name' => '1ro Básico', 'sections' => ['A', 'B']],
            ['id' => 5, 'level' => 'Básico', 'name' => '2do Básico', 'sections' => ['A', 'B']],
            ['id' => 6, 'level' => 'Básico', 'name' => '3ro Básico', 'sections' => ['A']],
        ];
        
        return $this->json(['success' => true, 'data' => $grades]);
    }

    #[Route('/sections', methods: ['GET'])]
    public function sections(): JsonResponse
    {
        $sections = [
            ['id' => 1, 'name' => 'A', 'schedule' => 'Matutina', 'capacity' => 30],
            ['id' => 2, 'name' => 'B', 'schedule' => 'Matutina', 'capacity' => 30],
            ['id' => 3, 'name' => 'C', 'schedule' => 'Vespertina', 'capacity' => 25],
        ];
        
        return $this->json(['success' => true, 'data' => $sections]);
    }

    #[Route('/subjects', methods: ['GET'])]
    public function subjects(): JsonResponse
    {
        $subjects = [
            ['id' => 1, 'code' => 'MAT01', 'name' => 'Matemáticas', 'hoursWeek' => 5],
            ['id' => 2, 'code' => 'ESP01', 'name' => 'Comunicación y Lenguaje', 'hoursWeek' => 5],
            ['id' => 3, 'code' => 'CN01', 'name' => 'Ciencias Naturales', 'hoursWeek' => 4],
            ['id' => 4, 'code' => 'CS01', 'name' => 'Ciencias Sociales', 'hoursWeek' => 3],
            ['id' => 5, 'code' => 'ING01', 'name' => 'Inglés', 'hoursWeek' => 4],
        ];
        
        return $this->json(['success' => true, 'data' => $subjects]);
    }

    #[Route('/teachers', methods: ['GET'])]
    public function teachers(): JsonResponse
    {
        $teachers = [
            ['id' => 1, 'code' => 'DOC-001', 'name' => 'Prof. Roberto García', 'specialization' => 'Matemáticas'],
            ['id' => 2, 'code' => 'DOC-002', 'name' => 'Profa. María López', 'specialization' => 'Comunicación'],
            ['id' => 3, 'code' => 'DOC-003', 'name' => 'Prof. Carlos Hernández', 'specialization' => 'Ciencias'],
        ];
        
        return $this->json(['success' => true, 'data' => $teachers]);
    }

    #[Route('/school-cycles', methods: ['GET'])]
    public function schoolCycles(): JsonResponse
    {
        $cycles = [
            ['id' => 1, 'year' => 2025, 'name' => 'Ciclo 2025', 'isCurrent' => true],
            ['id' => 2, 'year' => 2024, 'name' => 'Ciclo 2024', 'isCurrent' => false],
        ];
        
        return $this->json(['success' => true, 'data' => $cycles]);
    }

    #[Route('/academic-levels', methods: ['GET'])]
    public function academicLevels(): JsonResponse
    {
        $levels = [
            ['id' => 1, 'code' => 'PRE', 'name' => 'Preprimaria'],
            ['id' => 2, 'code' => 'PRI', 'name' => 'Primaria'],
            ['id' => 3, 'code' => 'BAS', 'name' => 'Básico'],
            ['id' => 4, 'code' => 'BAC', 'name' => 'Bachillerato'],
        ];
        
        return $this->json(['success' => true, 'data' => $levels]);
    }

    #[Route('/payment-methods', methods: ['GET'])]
    public function paymentMethods(): JsonResponse
    {
        $methods = [
            ['id' => 1, 'code' => 'EFECTIVO', 'name' => 'Efectivo'],
            ['id' => 2, 'code' => 'TARJETA_CREDITO', 'name' => 'Tarjeta de Crédito'],
            ['id' => 3, 'code' => 'TARJETA_DEBITO', 'name' => 'Tarjeta de Débito'],
            ['id' => 4, 'code' => 'DEPOSITO', 'name' => 'Depósito Bancario'],
            ['id' => 5, 'code' => 'TRANSFERENCIA', 'name' => 'Transferencia'],
        ];
        
        return $this->json(['success' => true, 'data' => $methods]);
    }

    #[Route('/document-types', methods: ['GET'])]
    public function documentTypes(): JsonResponse
    {
        $types = [
            ['id' => 1, 'code' => 'DPI', 'name' => 'DPI'],
            ['id' => 2, 'code' => 'CUI', 'name' => 'CUI Menor'],
            ['id' => 3, 'code' => 'PASAPORTE', 'name' => 'Pasaporte'],
            ['id' => 4, 'code' => 'CERT_NAC', 'name' => 'Certificado de Nacimiento'],
        ];
        
        return $this->json(['success' => true, 'data' => $types]);
    }

    #[Route('/relationships', methods: ['GET'])]
    public function relationships(): JsonResponse
    {
        $relationships = [
            ['id' => 1, 'code' => 'PADRE', 'name' => 'Padre'],
            ['id' => 2, 'code' => 'MADRE', 'name' => 'Madre'],
            ['id' => 3, 'code' => 'ABUELO', 'name' => 'Abuelo/a'],
            ['id' => 4, 'code' => 'TIO', 'name' => 'Tío/a'],
            ['id' => 5, 'code' => 'HERMANO', 'name' => 'Hermano/a'],
            ['id' => 6, 'code' => 'OTRO', 'name' => 'Otro'],
        ];
        
        return $this->json(['success' => true, 'data' => $relationships]);
    }

    #[Route('/statuses', methods: ['GET'])]
    public function statuses(): JsonResponse
    {
        $statuses = [
            ['id' => 1, 'code' => 'ACTIVO', 'name' => 'Activo'],
            ['id' => 2, 'code' => 'INACTIVO', 'name' => 'Inactivo'],
            ['id' => 3, 'code' => 'PENDIENTE', 'name' => 'Pendiente'],
            ['id' => 4, 'code' => 'CANCELADO', 'name' => 'Cancelado'],
            ['id' => 5, 'code' => 'CERRADO', 'name' => 'Cerrado'],
        ];
        
        return $this->json(['success' => true, 'data' => $statuses]);
    }
}
