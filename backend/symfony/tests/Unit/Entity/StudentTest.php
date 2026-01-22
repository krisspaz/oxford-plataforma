<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Student;
use PHPUnit\Framework\TestCase;

class StudentTest extends TestCase
{
    public function testStudentProperties(): void
    {
        $student = new Student();
        
        $student->setFirstName('Juan');
        $student->setLastName('Perez');
        $student->setCarnet('2024-001');
        $student->setEmail('juan@oxford.edu');
        $student->setAcademicRiskScore(0.5);

        $this->assertEquals('Juan', $student->getFirstName());
        $this->assertEquals('Perez', $student->getLastName());
        $this->assertEquals('2024-001', $student->getCarnet());
        $this->assertEquals('juan@oxford.edu', $student->getEmail());
        $this->assertEquals(0.5, $student->getAcademicRiskScore());
        $this->assertEquals('Juan Perez', $student->getFullName());
    }
}
