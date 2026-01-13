<?php

namespace App\Tests\Service;

use App\Service\GradeService;
use PHPUnit\Framework\TestCase;

class GradeServiceTest extends TestCase
{
    private $gradeService;

    private $gradeRepository;

    protected function setUp(): void
    {
        $this->gradeRepository = $this->createMock(\App\Repository\GradeRecordRepository::class);
        $this->gradeService = new GradeService($this->gradeRepository);
    }

    public function testCalculateAverage()
    {
        $grades = [80, 90, 100];
        $average = $this->gradeService->calculateAverage($grades);
        $this->assertEquals(90, $average);
    }

    public function testIsPassing()
    {
        $this->assertTrue($this->gradeService->isPassing(60)); // Assuming 60 is pass
        $this->assertFalse($this->gradeService->isPassing(59));
    }

    public function testCalculateGPA()
    {
        // Example logic
        $this->assertEquals(4.0, $this->gradeService->convertToGPA(95));
        $this->assertEquals(3.0, $this->gradeService->convertToGPA(85));
    }
}
