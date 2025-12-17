<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task Creation/Update Request DTO
 */
class TaskRequest
{
    #[Assert\NotBlank(message: 'Title is required')]
    #[Assert\Length(min: 3, max: 255)]
    public string $title = '';

    #[Assert\Length(max: 5000)]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Task type is required')]
    #[Assert\Choice(choices: ['tarea', 'examen', 'proyecto', 'actividad'])]
    public string $type = 'tarea';

    #[Assert\NotBlank(message: 'Due date is required')]
    #[Assert\Date]
    public string $dueDate = '';

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 1000)]
    public int $points = 100;

    #[Assert\NotBlank(message: 'Teacher ID is required')]
    #[Assert\Positive]
    public int $teacherId = 0;

    #[Assert\NotBlank(message: 'Subject ID is required')]
    #[Assert\Positive]
    public int $subjectId = 0;

    #[Assert\NotBlank(message: 'Bimester ID is required')]
    #[Assert\Positive]
    public int $bimesterId = 0;

    #[Assert\Positive]
    public ?int $cycleId = null;

    #[Assert\NotBlank(message: 'At least one grade must be assigned')]
    #[Assert\Count(min: 1, minMessage: 'At least one grade is required')]
    public array $grades = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->title = $data['title'] ?? '';
        $dto->description = $data['description'] ?? null;
        $dto->type = $data['type'] ?? 'tarea';
        $dto->dueDate = $data['dueDate'] ?? '';
        $dto->points = (int)($data['points'] ?? 100);
        $dto->teacherId = (int)($data['teacherId'] ?? 0);
        $dto->subjectId = (int)($data['subjectId'] ?? 0);
        $dto->bimesterId = (int)($data['bimesterId'] ?? 0);
        $dto->cycleId = isset($data['cycleId']) ? (int)$data['cycleId'] : null;
        $dto->grades = $data['grades'] ?? [];
        return $dto;
    }
}
