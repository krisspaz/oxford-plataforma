<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StudentRequest
{
    #[Assert\NotBlank]
    public string $firstName;
    
    #[Assert\NotBlank]
    public string $lastName;
    
    #[Assert\NotBlank]
    public string $code;
    
    public ?int $gradeId = null;
    public ?int $sectionId = null;
    public ?int $familyId = null;
    public ?string $birthDate = null;
    public ?string $gender = null;
    public ?string $address = null;
}
