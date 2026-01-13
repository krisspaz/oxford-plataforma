<?php
// fix_teacher.php
require 'vendor/autoload.php';

use App\Kernel;
use App\Entity\Teacher;
use App\Entity\Subject;
use App\Entity\Grade;
use App\Entity\AcademicLevel;
use App\Entity\SubjectAssignment;
use App\Entity\SchoolCycle;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();

$email = 'teacher@oxford.edu';
$teacher = $em->getRepository(Teacher::class)->findOneBy(['email' => $email]);

if (!$teacher) {
    echo "Creating profile for $email...\n";
    $teacher = new Teacher();
    $teacher->setFirstName("Juan");
    $teacher->setLastName("Pérez");
    $teacher->setEmail($email);
    $teacher->setEmployeeCode("DOC-2026-001");
    $teacher->setSpecialization("Matemáticas");
    $teacher->setContractType("Tiempo Completo");
    $teacher->setIsActive(true);
    $em->persist($teacher);
    $em->flush();
}

echo "Using Teacher ID: " . $teacher->getId() . "\n";

// Ensure AcademicLevel exists
$level = $em->getRepository(AcademicLevel::class)->findOneBy(['name' => 'Bachillerato']);
if (!$level) {
    $level = new AcademicLevel();
    $level->setName("Bachillerato");
    $level->setIsActive(true);
    $em->persist($level);
}

// Ensure Grade (Level) exists
$grade = $em->getRepository(Grade::class)->findOneBy(['name' => '5to Bachillerato']);
if (!$grade) {
    $grade = new Grade();
    $grade->setName("5to Bachillerato");
    $grade->setLevel($level);
    $grade->setIsActive(true);
    $em->persist($grade);
}

// Ensure Subject exists
$math = $em->getRepository(Subject::class)->findOneBy(['name' => 'Matemáticas']);
if (!$math) {
    $math = new Subject();
    $math->setName("Matemáticas");
    $math->setCode("MAT101");
    $em->persist($math);
}

// Ensure SchoolCycle
$cycle = $em->getRepository(SchoolCycle::class)->findOneBy(['name' => '2026']);
if (!$cycle) {
    $cycle = new SchoolCycle();
    $cycle->setName("2026");
    $cycle->setStartDate(new \DateTime('2026-01-01'));
    $cycle->setEndDate(new \DateTime('2026-10-31'));
    $cycle->setIsActive(true);
    $em->persist($cycle);
}

$em->flush(); // Flush dependencies first

// Create Assignment if not exists
$existingAssignment = $em->getRepository(SubjectAssignment::class)->findOneBy([
    'teacher' => $teacher,
    'subject' => $math,
    'grade' => $grade
]);

if (!$existingAssignment) {
    $assignment = new SubjectAssignment();
    $assignment->setTeacher($teacher);
    $assignment->setSubject($math);
    $assignment->setGrade($grade);
    $assignment->setSchoolCycle($cycle);
    $assignment->setHoursPerWeek(5);
    $assignment->setIsActive(true);
    $em->persist($assignment);
    $em->flush();
    echo "Assigned Matemáticas to 5to Bachillerato.\n";
} else {
    echo "Assignment already exists.\n";
}
