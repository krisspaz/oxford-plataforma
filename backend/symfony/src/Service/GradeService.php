<?php

namespace App\Service;

use App\Repository\GradeRecordRepository;

class GradeService
{
    private $gradeRepository;

    public function __construct(GradeRecordRepository $gradeRepository)
    {
        $this->gradeRepository = $gradeRepository;
    }

    public function calculateAverage(array $grades): float
    {
        if (empty($grades)) {
            return 0.0;
        }

        $sum = array_reduce($grades, fn($carry, $item) => $carry + $item, 0);
        return round($sum / count($grades), 2);
    }

    public function isPassing(float $grade): bool
    {
        // Configurable passing grade, default 60
        return $grade >= 60.0;
    }

    public function convertToGPA(float $grade): float
    {
        if ($grade >= 90) return 4.0;
        if ($grade >= 80) return 3.0;
        if ($grade >= 70) return 2.0;
        if ($grade >= 60) return 1.0;
        return 0.0;
    }

    public function getStudentBimesterGrades(int $studentId, int $year): array
    {
        // Real logic to fetch from repo would go here
        // For now, we structure the data transformation logic
        $records = $this->gradeRepository->findByStudentAndYear($studentId, $year);
        
        $report = [];
        foreach ($records as $record) {
            $subject = $record->getSubject()->getName();
            if (!isset($report[$subject])) {
                $report[$subject] = ['subject' => $subject, 'b1' => null, 'b2' => null, 'b3' => null, 'b4' => null];
            }
            // Map period (BI_1, BI_2...) to array keys
            $periodKey = strtolower(str_replace('BI_', 'b', $record->getPeriod()));
            if (isset($report[$subject][$periodKey])) {
                $report[$subject][$periodKey] = $record->getGrade();
            }
        }
        
        return array_values($report);
    }
}
