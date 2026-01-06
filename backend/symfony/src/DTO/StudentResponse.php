<?php

namespace App\DTO;

class StudentResponse
{
    public function __construct(
        public int $id,
        public string $code,
        public string $firstName,
        public string $lastName,
        public ?string $email = null,
        public ?array $grade = null,
        public ?array $section = null,
        public ?array $family = null,
        public ?string $status = 'active',
    ) {}
    
    public static function fromEntity($student): self
    {
        return new self(
            id: $student->getId(),
            code: $student->getCode(),
            firstName: $student->getUser()?->getFirstName() ?? '',
            lastName: $student->getUser()?->getLastName() ?? '',
            email: $student->getUser()?->getEmail(),
            grade: $student->getGrade() ? [
                'id' => $student->getGrade()->getId(),
                'name' => $student->getGrade()->getName(),
            ] : null,
            section: $student->getSection() ? [
                'id' => $student->getSection()->getId(),
                'name' => $student->getSection()->getName(),
            ] : null,
            status: $student->getStatus(),
        );
    }
}
