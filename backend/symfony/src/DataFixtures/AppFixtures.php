<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\Course;
use App\Entity\SchoolCycle;
use App\Entity\Task;
use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roles = [
            'admin@oxford.edu' => [
                'ROLE_ADMIN', 
                'ROLE_DIRECTOR', 
                'ROLE_COORDINATION', 
                'ROLE_SECRETARY', 
                'ROLE_ACCOUNTANT', 
                'ROLE_INFORMATICS',
                'ROLE_TEACHER',
                'ROLE_STUDENT',
                'ROLE_PARENT'
            ],
            'teacher@oxford.edu' => ['ROLE_TEACHER'],
            'student@oxford.edu' => ['ROLE_STUDENT'],
            'parent@oxford.edu' => ['ROLE_PARENT'],
            'secretary@oxford.edu' => ['ROLE_SECRETARY'],
            'accountant@oxford.edu' => ['ROLE_ACCOUNTANT'],
            'informatics@oxford.edu' => ['ROLE_INFORMATICS'],
            'coordination@oxford.edu' => ['ROLE_COORDINATION'],
            'director@oxford.edu' => ['ROLE_DIRECTOR'],
        ];

        foreach ($roles as $email => $role) {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles($role);
            $password = $this->hasher->hashPassword($user, 'oxford123'); // Standard Password
            $user->setPassword($password);
            $manager->persist($user);
        }

        // 1. School Cycle
        $cycle = new SchoolCycle();
        $cycle->setName('Ciclo Escolar 2025');
        $cycle->setStartDate(new \DateTime('2025-01-15'));
        $cycle->setEndDate(new \DateTime('2025-10-31'));
        $cycle->setIsActive(true);
        $manager->persist($cycle);

        // 2. Course
        $course = new Course();
        $course->setName('Primero Básico A');
        $course->setGradeLevel('1ro Básico');
        $course->setSection('A');
        $manager->persist($course);

        // 3. Student
        for ($i = 0; $i < 10; $i++) {
            $student = new Student();
            $student->setFirstName('Estudiante ' . ($i + 1));
            $student->setLastName('Apellido ' . ($i + 1));
            $student->setCarnet('2025-' . str_pad($i, 4, '0', STR_PAD_LEFT));
            $student->setEmail('student' . $i . '@oxford.edu');
            $student->setBirthDate(new \DateTime('2010-05-15')); // Fixed birthdate
            $student->setSchoolCycle($cycle);
            $student->setCourse($course);
            $student->setAcademicRiskScore(rand(0, 100) / 100); // 0.0 to 1.0
            $manager->persist($student);
            
            // 4. Payment (Some Late)
            $payment = new Payment();
            $payment->setStudent($student);
            $payment->setAmount(500.00);
            $payment->setConcept('Mensualidad Enero');
            $payment->setDueDate(new \DateTime('2025-01-31')); // In the past
            // Randomly paid or unpaid
            if ($i % 3 == 0) {
                 $payment->setStatus('PENDING'); // Overdue
            } else {
                 $payment->setStatus('PAID');
                 $payment->setPaidAt(new \DateTime('2025-01-25'));
            }
            $manager->persist($payment);
        }

        // 5. Tasks (commented out - entity structure changed)
        // $task = new Task();
        // $task->setTitle('Tarea de Matemáticas #1');
        // $task->setDescription('Resolver ejercicios 1-10 de la página 45.');
        // $task->setDueDate(new \DateTime('2025-02-20 23:59:00'));
        // $task->setStatus('PENDING');
        // $task->setCourse($course);
        // $manager->persist($task);

        // 5. Teachers
        $teachers = [];
        $teacherNames = ['Prof. García', 'Prof. López', 'Prof. Martínez', 'Prof. Hernández', 'Prof. Smith'];
        foreach ($teacherNames as $i => $name) {
            $teacher = new \App\Entity\Teacher(); // Use FQCN or import
            $teacher->setFirstName(explode(' ', $name)[1]);
            $teacher->setLastName('Docente');
            $teacher->setEmail('teacher' . ($i + 1) . '@oxford.edu');
            $teacher->setEmployeeCode('EMP-' . ($i + 100));
            $teacher->setContractType('Tiempo Completo');
            $manager->persist($teacher);
            $teachers[] = $teacher;
        }

        // 6. Subjects
        $subjectsData = [
            ['name' => 'Matemáticas', 'code' => 'MAT101'],
            ['name' => 'Ciencias Naturales', 'code' => 'CN101'],
            ['name' => 'Comunicación y Lenguaje', 'code' => 'CL101'],
            ['name' => 'Estudios Sociales', 'code' => 'SOC101'],
            ['name' => 'Inglés', 'code' => 'ING101'],
            ['name' => 'Educación Física', 'code' => 'EF101'],
            ['name' => 'Arte y Música', 'code' => 'ART101'],
            ['name' => 'Computación', 'code' => 'COMP101'],
        ];

        foreach ($subjectsData as $data) {
            $subject = new \App\Entity\Subject();
            $subject->setName($data['name']);
            $subject->setCode($data['code']);
            $manager->persist($subject);
        }

        $manager->flush();
    }
}
