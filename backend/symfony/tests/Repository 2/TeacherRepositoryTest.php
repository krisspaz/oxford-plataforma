<?php

namespace App\Tests\Repository;

use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeacherRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?TeacherRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->repository = $this->entityManager->getRepository(Teacher::class);
    }

    public function testFindAllTeachers(): void
    {
        $teachers = $this->repository->findAll();
        
        // Should return an array
        $this->assertIsArray($teachers);
    }

    public function testFindBySpecialization(): void
    {
        $teachers = $this->repository->findBy(['specialization' => 'Matemáticas']);
        
        foreach ($teachers as $teacher) {
            $this->assertEquals('Matemáticas', $teacher->getSpecialization());
        }
    }

    public function testTeacherEntityValidation(): void
    {
        $teacher = new Teacher();
        $teacher->setFirstName('María');
        $teacher->setLastName('García');
        $teacher->setEmail('maria.garcia@oxford.edu');
        $teacher->setEmployeeCode('DOC-001');
        $teacher->setSpecialization('Ciencias');
        $teacher->setContractType('Tiempo Completo');
        
        $this->assertEquals('María', $teacher->getFirstName());
        $this->assertEquals('García', $teacher->getLastName());
        $this->assertEquals('María García', $teacher->getFullName());
        $this->assertEquals('DOC-001', $teacher->getEmployeeCode());
        $this->assertEquals('Ciencias', $teacher->getSpecialization());
        $this->assertEquals('Tiempo Completo', $teacher->getContractType());
    }

    public function testTeacherDefaultContractType(): void
    {
        $teacher = new Teacher();
        $this->assertEquals('Tiempo Completo', $teacher->getContractType());
    }

    public function testTeacherSubjectAssignmentsCollection(): void
    {
        $teacher = new Teacher();
        $assignments = $teacher->getSubjectAssignments();
        
        $this->assertCount(0, $assignments);
        $this->assertTrue($assignments->isEmpty());
    }

    public function testFindActiveTeachers(): void
    {
        $teachers = $this->repository->findBy(['isActive' => true]);
        
        foreach ($teachers as $teacher) {
            $this->assertTrue($teacher->isActive());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
