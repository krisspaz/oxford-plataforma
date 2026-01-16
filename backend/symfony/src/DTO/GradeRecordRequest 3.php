<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GradeRecordRequest
{
    #[Assert\NotBlank]
    public int $studentId;
    
    #[Assert\NotBlank]
    public int $subjectId;
    
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 100)]
    public float $grade;
    
    #[Assert\NotBlank]
    public string $period;
    
    public ?string $comments = null;
}
