<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attendance Batch Save Request DTO
 */
class AttendanceBatchRequest
{
    #[Assert\NotBlank(message: 'Schedule ID is required')]
    #[Assert\Positive]
    public int $scheduleId = 0;

    #[Assert\NotBlank(message: 'Date is required')]
    #[Assert\Date]
    public string $date = '';

    #[Assert\NotBlank(message: 'Attendance records are required')]
    #[Assert\Count(min: 1, minMessage: 'At least one attendance record is required')]
    #[Assert\All([
        new Assert\Collection([
            'studentId' => [new Assert\NotBlank(), new Assert\Positive()],
            'status' => [new Assert\NotBlank(), new Assert\Choice(['present', 'absent', 'late', 'excused'])],
            'notes' => new Assert\Optional([new Assert\Length(max: 500)]),
        ])
    ])]
    public array $attendances = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->scheduleId = (int)($data['scheduleId'] ?? 0);
        $dto->date = $data['date'] ?? '';
        $dto->attendances = $data['attendances'] ?? [];
        return $dto;
    }
}
