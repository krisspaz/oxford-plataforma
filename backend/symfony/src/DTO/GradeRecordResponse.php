<?php

namespace App\DTO;

class GradeRecordResponse
{
    public function __construct(
        public int $id,
        public int $studentId,
        public string $studentName,
        public int $subjectId,
        public string $subjectName,
        public float $grade,
        public string $period,
        public string $status,
        public ?string $comments = null,
    ) {}
}
