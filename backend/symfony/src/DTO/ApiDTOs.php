<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Request/Response DTOs for API consistency
 */

// ==========================================
// AUTH DTOs
// ==========================================

class LoginRequest
{
    #[Assert\NotBlank(message: 'Email es requerido')]
    #[Assert\Email(message: 'Email inválido')]
    public string $email;
    
    #[Assert\NotBlank(message: 'Contraseña es requerida')]
    #[Assert\Length(min: 6, minMessage: 'Contraseña muy corta')]
    public string $password;
    
    public ?string $twoFactorCode = null;
}

class LoginResponse
{
    public function __construct(
        public string $token,
        public ?string $refreshToken = null,
        public ?array $user = null,
        public bool $requiresTwoFactor = false,
    ) {}
}

class RegisterRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
    
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
    
    #[Assert\NotBlank]
    public string $firstName;
    
    #[Assert\NotBlank]
    public string $lastName;
    
    public ?string $phone = null;
}

// ==========================================
// STUDENT DTOs
// ==========================================

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

// ==========================================
// GRADE DTOs
// ==========================================

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

// ==========================================
// PAGINATION DTOs
// ==========================================

class PaginatedResponse
{
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages,
    ) {}
    
    public static function create(array $items, int $total, int $page, int $limit): self
    {
        return new self(
            items: $items,
            total: $total,
            page: $page,
            limit: $limit,
            totalPages: (int) ceil($total / $limit),
        );
    }
}

// ==========================================
// ERROR DTOs
// ==========================================

class ErrorResponse
{
    public function __construct(
        public string $error,
        public string $message,
        public int $statusCode,
        public ?array $details = null,
        public ?string $traceId = null,
    ) {}
}

class ValidationErrorResponse
{
    public function __construct(
        public string $error = 'validation_error',
        public string $message = 'Datos inválidos',
        public array $errors = [],
    ) {}
}

// ==========================================
// NOTIFICATION DTOs
// ==========================================

class NotificationResponse
{
    public function __construct(
        public int $id,
        public string $type,
        public string $title,
        public string $message,
        public bool $read,
        public string $createdAt,
    ) {}
}

// ==========================================
// API RESPONSE WRAPPER
// ==========================================

class ApiResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public ?array $meta = null,
    ) {}
    
    public static function success(mixed $data, ?string $message = null, ?array $meta = null): self
    {
        return new self(true, $data, $message, $meta);
    }
    
    public static function error(string $message, mixed $data = null): self
    {
        return new self(false, $data, $message);
    }
}
