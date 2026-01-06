<?php

namespace App\Service;

class GradeCalculatorService
{
    public function calculateFinalAverage(array $bimesterGrades): float
    {
        // Complex calculation logic
        return array_sum($bimesterGrades) / count($bimesterGrades);
    }
}
