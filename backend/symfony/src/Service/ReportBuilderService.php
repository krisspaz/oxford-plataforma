<?php

namespace App\Service;

class ReportBuilderService
{
    public function buildAcademicReport(int $studentId, int $year): array
    {
        return ['data' => 'pdf_content_stub'];
    }
}
