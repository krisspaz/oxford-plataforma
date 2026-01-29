<?php

namespace App\DataFixtures;

use App\Entity\Tenant;
use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\SubjectAssignment;
use App\Entity\Enrollment;
use App\Entity\Bimester;
use App\Entity\SchoolCycle;
use App\Entity\AcademicLevel;
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
        // 1. Ensure Tenant "Oxford" exists
        $tenantRepo = $manager->getRepository(Tenant::class);
        $oxfordTenant = $tenantRepo->findOneBy(['slug' => 'oxford']);
        
        if (!$oxfordTenant) {
            $oxfordTenant = new Tenant();
            $oxfordTenant->setName('Colegio Oxford');
            $oxfordTenant->setSlug('oxford');
            $oxfordTenant->setDomain('oxford.sistema.com');
            $oxfordTenant->setIsActive(true);
            $oxfordTenant->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($oxfordTenant);
            $manager->flush(); // Flush to get ID if needed
        }

        // Create Users with different roles
        $users = [
            // Administración
            ['email' => 'admin@oxford.edu.gt', 'role' => User::ROLE_SUPER_ADMIN, 'name' => 'Administrador Sistema', 'password' => 'oxford2026'],
            ['email' => 'krispaz77@gmail.com', 'role' => User::ROLE_SUPER_ADMIN, 'name' => 'Kristopher Paz', 'password' => 'oxford2026'],
            
            // Dirección
            ['email' => 'direccion@colegiooxford.edu.gt', 'role' => 'ROLE_DIRECCION', 'name' => 'Adriana Alvarado', 'password' => 'oxford2026'],
            
            // Coordinación
            ['email' => 'coordinacion@colegiooxford.edu.gt', 'role' => 'ROLE_COORDINACION', 'name' => 'Coordinación Académica', 'password' => 'oxford2026'],
            
            // Docentes
            ['email' => 'sindry@colegiooxford.edu.gt', 'role' => 'ROLE_DOCENTE', 'name' => 'Sindry Botzoc', 'password' => 'oxford2026'],
            ['email' => 'docente1@oxford.edu', 'role' => 'ROLE_DOCENTE', 'name' => 'Juan Pérez García', 'password' => 'oxford123'],
            
            // Secretaría
            ['email' => 'secretaria@oxford.edu', 'role' => 'ROLE_SECRETARIA', 'name' => 'Laura Gómez', 'password' => 'oxford123'],
            
            // Alumno
            ['email' => 'julia@colegiooxford.edu.gt', 'role' => 'ROLE_ALUMNO', 'name' => 'Julia Quiroa', 'password' => 'oxford2026'],
            ['email' => 'alumno@oxford.edu', 'role' => 'ROLE_ALUMNO', 'name' => 'Diego Morales', 'password' => 'oxford123'],
            
            // Padre de familia
            ['email' => 'padre@oxford.edu', 'role' => 'ROLE_PADRE', 'name' => 'Fernando Morales', 'password' => 'oxford123'],
        ];


        foreach ($users as $userData) {
            // Skip if user already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $userData['email']]);
            if ($existingUser) {
                // UPDATE tenant if missing?
                if (method_exists($existingUser, 'getTenant') && !$existingUser->getTenant()) {
                    $existingUser->setTenant($oxfordTenant);
                    $manager->persist($existingUser);
                }
                continue;
            }
            
            $u = new User();
            $u->setEmail($userData['email']);
            $u->setRoles([$userData['role']]);
            $u->setName($userData['name']);
            // Assign Tenant explicitly
            $u->setTenant($oxfordTenant);
            
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
            $student->setTenant($oxfordTenant);
            $student->setFirstName($studentData['firstName']);
            $student->setLastName($studentData['lastName']);
            $student->setCarnet($studentData['carnet']);
            $student->setBirthDate(new \DateTime('2010-' . str_pad(($index % 12) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($index % 28) + 1, 2, '0', STR_PAD_LEFT)));
            $student->setEmail(strtolower($studentData['firstName']) . '.' . explode(' ', $studentData['lastName'])[0] . '@estudiante.oxford.edu');
            $student->setIsActive(true);
            $manager->persist($student);
        }

        $manager->flush(); // Flush users first

        // Create Teacher Profiles
        $teacherRepository = $manager->getRepository(Teacher::class);
        $subjectRepository = $manager->getRepository(Subject::class);
        $gradeRepository = $manager->getRepository(Grade::class);
        
        // Ensure Subjects and Grades exist (if not created by other fixtures)
        // Creating basic subjects if missing
        $subjectsList = ['Matemáticas', 'Lenguaje', 'Ciencias Naturales', 'Ciencias Sociales', 'Inglés', 'Artes'];
        $subjects = [];
        foreach ($subjectsList as $subName) {
            $s = $subjectRepository->findOneBy(['name' => $subName]);
            if (!$s) {
                $s = new Subject();
                $s->setTenant($oxfordTenant);
                $s->setName($subName);
                $s->setCode(strtoupper(substr($subName, 0, 3)));
                $manager->persist($s);
            }
            $subjects[] = $s;
        }

        // Creating Academic Levels
        $levelNames = ['Preprimaria', 'Primaria', 'Básico', 'Diversificado'];
        $levels = [];
        $levelRepository = $manager->getRepository(AcademicLevel::class);
        
        foreach ($levelNames as $lName) {
            $l = $levelRepository->findOneBy(['name' => $lName]);
            if (!$l) {
                $l = new AcademicLevel();
                $l->setTenant($oxfordTenant);
                $l->setName($lName);
                $l->setCode(strtoupper(substr($lName, 0, 3)));
                $l->setIsActive(true);
                $manager->persist($l);
            }
            $levels[$lName] = $l;
        }
        $manager->flush(); // Need IDs for levels

        // Create Current School Cycle
        $cycleRepository = $manager->getRepository(SchoolCycle::class);
        $currentYear = date('Y');
        $cycle = $cycleRepository->findOneBy(['name' => $currentYear]);
        if (!$cycle) {
            $cycle = new SchoolCycle();
            $cycle->setTenant($oxfordTenant);
            $cycle->setName($currentYear);
            $cycle->setStartDate(new \DateTime($currentYear . '-01-15'));
            $cycle->setEndDate(new \DateTime($currentYear . '-10-31'));
            $cycle->setIsActive(true);
            $manager->persist($cycle);
        }
        $manager->flush();

        // Creating basic grades if missing
        $gradesList = ['1ro Primaria', '2do Primaria', '3ro Primaria', '4to Primaria', '5to Primaria', '6to Primaria'];
        $grades = [];
        foreach ($gradesList as $gradeName) {
            $g = $gradeRepository->findOneBy(['name' => $gradeName]);
            if (!$g) {
                $g = new Grade();
                $g->setTenant($oxfordTenant);
                $g->setName($gradeName);
                $g->setLevel($levels['Primaria']);
                $manager->persist($g);
            }
            $grades[] = $g;
        }
        $manager->flush();

        // Link Users to Teacher Profiles
        $allUsers = $this->userRepository->findAll();
        $docentes = array_filter($allUsers, function($u) {
            return in_array('ROLE_DOCENTE', $u->getRoles());
        });

        foreach ($docentes as $user) {
            $teacher = $teacherRepository->findOneBy(['email' => $user->getEmail()]);
            if (!$teacher) {
                $teacher = new Teacher();
                $teacher->setTenant($oxfordTenant);
                $teacher->setUser($user);
                $teacher->setFirstName(explode(' ', $user->getName())[0]);
                $teacher->setLastName(substr($user->getName(), strpos($user->getName(), ' ') + 1));
                $teacher->setEmail($user->getEmail());
                $teacher->setEmployeeCode('EMP-' . rand(1000, 9999));
                $teacher->setSpecialization('General');
                $teacher->setPhone('5555-0000');
                $teacher->setContractType('Full-time');
                $teacher->setHireDate(new \DateTime('-1 year'));
                $teacher->setIsActive(true);
                $manager->persist($teacher);
                
                // Assign random subjects
                if ($user->getEmail() === 'sindry@colegiooxford.edu.gt') {
                     // Assign specific subjects to Sindry for demo
                     for ($i = 0; $i < 3; $i++) {
                         $sa = new SubjectAssignment();
                         $sa->setTenant($oxfordTenant);
                         $sa->setTeacher($teacher);
                         $sa->setSubject($subjects[$i]);
                         $sa->setGrade($grades[0]); // 1ro Primaria
                         $sa->setSchoolCycle($cycle);
                         $sa->setHoursPerWeek(5);
                         $sa->setIsActive(true);
                         $manager->persist($sa);
                     }
                } else {
                    // Random assignment for others
                     $sa = new SubjectAssignment();
                     $sa->setTenant($oxfordTenant);
                     $sa->setTeacher($teacher);
                     $sa->setSubject($subjects[array_rand($subjects)]);
                     $sa->setGrade($grades[array_rand($grades)]);
                     $sa->setSchoolCycle($cycle);
                     $sa->setIsActive(true);
                     $manager->persist($sa);
                }
            }
        }
        
        $manager->flush();

        // Enroll all students in 1st Grade for demo purposes
        $allStudents = $manager->getRepository(Student::class)->findAll();
        foreach ($allStudents as $student) {
            $existingEnrollment = $manager->getRepository(Enrollment::class)->findOneBy(['student' => $student, 'schoolCycle' => $cycle]);
            if (!$existingEnrollment) {
                $enrollment = new Enrollment();
                $enrollment->setTenant($oxfordTenant);
                $enrollment->setStudent($student);
                $enrollment->setSchoolCycle($cycle);
                $enrollment->setGrade($grades[0]); // 1ro Primaria
                $enrollment->setEnrollmentDate(new \DateTime());
                $enrollment->setStatus('INSCRITO');
                $manager->persist($enrollment);
            }
        }
        $manager->flush();
    }
}

