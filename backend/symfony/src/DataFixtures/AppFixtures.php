<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Bimester;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Create Users with different roles
        $users = [
            // Administración
            ['email' => 'admin@oxford.edu', 'role' => User::ROLE_SUPER_ADMIN, 'name' => 'Super Admin', 'password' => $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123'],
            
            // Dirección
            ['email' => 'director@oxford.edu', 'role' => 'ROLE_DIRECTOR', 'name' => 'Pedro Hernández', 'password' => 'oxford123'],
            
            // Coordinación
            ['email' => 'coordinacion@oxford.edu', 'role' => 'ROLE_COORDINACION', 'name' => 'María López', 'password' => 'oxford123'],
            
            // Docentes
            ['email' => 'docente1@oxford.edu', 'role' => 'ROLE_DOCENTE', 'name' => 'Juan Pérez García', 'password' => 'oxford123'],
            ['email' => 'docente2@oxford.edu', 'role' => 'ROLE_DOCENTE', 'name' => 'Ana Martínez Ruiz', 'password' => 'oxford123'],
            ['email' => 'docente3@oxford.edu', 'role' => 'ROLE_DOCENTE', 'name' => 'Roberto Sánchez', 'password' => 'oxford123'],
            
            // Secretaría
            ['email' => 'secretaria@oxford.edu', 'role' => 'ROLE_SECRETARIA', 'name' => 'Laura Gómez', 'password' => 'oxford123'],
            
            // Contabilidad
            ['email' => 'contabilidad@oxford.edu', 'role' => 'ROLE_CONTABILIDAD', 'name' => 'Pedro Ramírez', 'password' => 'oxford123'],
            
            // Alumno
            ['email' => 'alumno@oxford.edu', 'role' => 'ROLE_ALUMNO', 'name' => 'Diego Morales', 'password' => 'oxford123'],
            
            // Padre de familia
            ['email' => 'padre@oxford.edu', 'role' => 'ROLE_PADRE', 'name' => 'Fernando Morales', 'password' => 'oxford123'],
        ];


        foreach ($users as $userData) {
            // Skip if user already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $userData['email']]);
            if ($existingUser) {
                continue;
            }
            
            $u = new User();
            $u->setEmail($userData['email']);
            $u->setRoles([$userData['role']]);
            $u->setName($userData['name']);
            $passToHash = $userData['password'] ?? $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123';
            $password = $this->passwordHasher->hashPassword($u, $passToHash);
            $u->setPassword($password); 
            $manager->persist($u);
        }

        // Create 15 Students for Teacher Dashboard
        $students = [
            ['firstName' => 'Carlos', 'lastName' => 'García López', 'carnet' => '2026-001'],
            ['firstName' => 'María', 'lastName' => 'Rodríguez Pérez', 'carnet' => '2026-002'],
            ['firstName' => 'José', 'lastName' => 'Hernández Ruiz', 'carnet' => '2026-003'],
            ['firstName' => 'Ana', 'lastName' => 'Martínez Sánchez', 'carnet' => '2026-004'],
            ['firstName' => 'Pedro', 'lastName' => 'López García', 'carnet' => '2026-005'],
            ['firstName' => 'Laura', 'lastName' => 'González Torres', 'carnet' => '2026-006'],
            ['firstName' => 'Diego', 'lastName' => 'Ramírez Castro', 'carnet' => '2026-007'],
            ['firstName' => 'Sofía', 'lastName' => 'Morales Vega', 'carnet' => '2026-008'],
            ['firstName' => 'Andrés', 'lastName' => 'Jiménez Flores', 'carnet' => '2026-009'],
            ['firstName' => 'Valentina', 'lastName' => 'Torres Mendoza', 'carnet' => '2026-010'],
            ['firstName' => 'Miguel', 'lastName' => 'Castillo Rivera', 'carnet' => '2026-011'],
            ['firstName' => 'Isabella', 'lastName' => 'Vargas Herrera', 'carnet' => '2026-012'],
            ['firstName' => 'Daniel', 'lastName' => 'Ortiz Aguilar', 'carnet' => '2026-013'],
            ['firstName' => 'Camila', 'lastName' => 'Reyes Guzmán', 'carnet' => '2026-014'],
            ['firstName' => 'Sebastián', 'lastName' => 'Cruz Romero', 'carnet' => '2026-015'],
        ];

        $studentRepository = $manager->getRepository(Student::class);
        
        foreach ($students as $index => $studentData) {
            // Skip if student already exists
            $existingStudent = $studentRepository->findOneBy(['carnet' => $studentData['carnet']]);
            if ($existingStudent) {
                continue;
            }
            
            $student = new Student();
            $student->setFirstName($studentData['firstName']);
            $student->setLastName($studentData['lastName']);
            $student->setCarnet($studentData['carnet']);
            $student->setBirthDate(new \DateTime('2010-' . str_pad(($index % 12) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($index % 28) + 1, 2, '0', STR_PAD_LEFT)));
            $student->setEmail(strtolower($studentData['firstName']) . '.' . explode(' ', $studentData['lastName'])[0] . '@estudiante.oxford.edu');
            $student->setIsActive(true);
            $manager->persist($student);
        }

        $manager->flush();
    }
}

