<?php

namespace App\Tests\Service;

use App\Entity\Student;
use App\Service\StudentService;
use App\Repository\StudentRepository;
use PHPUnit\Framework\TestCase;

class StudentServiceTest extends TestCase
{
    private $studentRepository;
    private $studentService;

    protected function setUp(): void
    {
        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->studentService = new StudentService($this->studentRepository);
    }

    public function testGetActiveStudents()
    {
        $this->studentRepository->expects($this->once())
            ->method('findBy')
            ->with(['status' => 'active'])
            ->willReturn([new Student(), new Student()]);

        $result = $this->studentService->getActiveStudents();
        $this->assertCount(2, $result);
    }

    public function testGenerateStudentCode()
    {
        // Mock repository to return null (no collision)
        $this->studentRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $code = $this->studentService->generateStudentCode(2026);
        $this->assertStringStartsWith('ST-2026-', $code);
    }
}
