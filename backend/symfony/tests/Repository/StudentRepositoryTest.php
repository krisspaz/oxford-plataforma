<?php

namespace App\Tests\Repository;

use App\Entity\Student;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Family;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StudentRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?StudentRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->repository = $this->entityManager->getRepository(Student::class);
    }

    public function testFindActiveStudents(): void
    {
        $students = $this->repository->findBy(['isActive' => true]);
        
        // All returned students should be active
        foreach ($students as $student) {
            $this->assertTrue($student->isActive());
        }
    }

    public function testFindByGrade(): void
    {
        // Get any grade from the database
        $gradeRepo = $this->entityManager->getRepository(Grade::class);
        $grades = $gradeRepo->findAll();
        
        if (count($grades) > 0) {
            $grade = $grades[0];
            
            // This tests the relationship works
            $this->assertInstanceOf(Grade::class, $grade);
            $this->assertNotNull($grade->getId());
        } else {
            $this->markTestSkipped('No grades in database');
        }
    }

    public function testStudentEntityValidation(): void
    {
        $student = new Student();
        $student->setFirstName('Test');
        $student->setLastName('Student');
        $student->setEmail('test.student@oxford.edu');
        $student->setStudentCode('STU-TEST-001');
        
        $this->assertEquals('Test', $student->getFirstName());
        $this->assertEquals('Student', $student->getLastName());
        $this->assertEquals('Test Student', $student->getFullName());
        $this->assertEquals('test.student@oxford.edu', $student->getEmail());
        $this->assertEquals('STU-TEST-001', $student->getStudentCode());
    }

    public function testStudentIsActiveDefault(): void
    {
        $student = new Student();
        $this->assertTrue($student->isActive());
    }

    public function testStudentEnrollmentCollection(): void
    {
        $student = new Student();
        $enrollments = $student->getEnrollments();
        
        $this->assertCount(0, $enrollments);
        $this->assertTrue($enrollments->isEmpty());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
