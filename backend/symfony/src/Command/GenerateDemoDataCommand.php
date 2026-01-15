<?php

namespace App\Command;

use App\Entity\AcademicLevel;
use App\Entity\Grade;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:generate-demo-data',
    description: 'Seeds the database with demo academic data',
)]
class GenerateDemoDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seeding Demo Data...');

        // 1. Levels
        $levels = [
            'Primaria' => 'PRIM',
            'Básico' => 'BAS',
            'Diversificado' => 'DIV'
        ];

        $levelEntities = [];
        foreach ($levels as $name => $code) {
            $level = $this->em->getRepository(AcademicLevel::class)->findOneBy(['code' => $code]);
            if (!$level) {
                $level = new AcademicLevel();
                $level->setName($name);
                $level->setCode($code);
                $this->em->persist($level);
                $io->text("Created Level: $name");
            }
            $levelEntities[$code] = $level;
        }

        // 2. Grades
        $grades = [
            ['name' => 'Primero Primaria', 'code' => '1PRI', 'level' => 'PRIM'],
            ['name' => 'Segundo Primaria', 'code' => '2PRI', 'level' => 'PRIM'],
            ['name' => 'Tero Primaria', 'code' => '3PRI', 'level' => 'PRIM'],
            ['name' => 'Primero Básico', 'code' => '1BAS', 'level' => 'BAS'],
            ['name' => 'Segundo Básico', 'code' => '2BAS', 'level' => 'BAS'],
            ['name' => 'Tercero Básico', 'code' => '3BAS', 'level' => 'BAS'],
            ['name' => '4to Bachillerato', 'code' => '4BAC', 'level' => 'DIV'],
            ['name' => '5to Bachillerato', 'code' => '5BAC', 'level' => 'DIV'],
        ];

        foreach ($grades as $g) {
            $grade = $this->em->getRepository(Grade::class)->findOneBy(['code' => $g['code']]);
            if (!$grade) {
                $grade = new Grade();
                $grade->setName($g['name']);
                $grade->setCode($g['code']);
                $grade->setLevel($levelEntities[$g['level']]);
                $this->em->persist($grade);
                $io->text("Created Grade: {$g['name']}");
            }
        }

        // 3. Subjects
        $subjects = [
            'Matemáticas' => 'MAT',
            'Idioma Español' => 'ESP',
            'Ciencias Naturales' => 'CN',
            'Ciencias Sociales' => 'CS',
            'Inglés' => 'ING',
            'Artes Plásticas' => 'ART',
            'Educación Física' => 'EFI',
            'Contabilidad' => 'CON',
            'Computación' => 'COMP',
            'Física Fundamental' => 'FIS',
        ];

        foreach ($subjects as $name => $code) {
            $subject = $this->em->getRepository(Subject::class)->findOneBy(['code' => $code]);
            if (!$subject) {
                $subject = new Subject();
                $subject->setName($name);
                $subject->setCode($code);
                $subject->setHoursWeek(5);
                $this->em->persist($subject);
                $io->text("Created Subject: $name");
            }
        }

        // 4. Teachers
        $teachers = [
            ['first' => 'Juan', 'last' => 'Pérez', 'specialty' => 'Matemáticas'],
            ['first' => 'Maria', 'last' => 'López', 'specialty' => 'Lenguaje'],
            ['first' => 'Carlos', 'last' => 'Ruiz', 'specialty' => 'Ciencias'],
            ['first' => 'Ana', 'last' => 'García', 'specialty' => 'Inglés'],
            ['first' => 'Pedro', 'last' => 'Sánchez', 'specialty' => 'Computación'],
        ];

        foreach ($teachers as $t) {
            $existing = $this->em->getRepository(Teacher::class)->findOneBy(['firstName' => $t['first'], 'lastName' => $t['last']]);
            if (!$existing) {
                $teacher = new Teacher();
                $teacher->setFirstName($t['first']);
                $teacher->setLastName($t['last']);
                $teacher->setSpecialization($t['specialty']);
                $teacher->setContractType('Tiempo Completo');
                // Optional: Create User for teacher if needed for login
                $this->em->persist($teacher);
                $io->text("Created Teacher: {$t['first']} {$t['last']}");
            }
        }

        $this->em->flush();
        $io->success('Demo data seeded successfully.');

        return Command::SUCCESS;
    }
}
