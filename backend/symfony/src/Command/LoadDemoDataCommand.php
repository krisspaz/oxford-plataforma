<?php

namespace App\Command;

use App\Entity\SchoolActivity;
use App\Entity\Schedule;
use App\Entity\SchoolCycle;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\Subject;
use App\Entity\Course;
use App\Entity\Classroom;
use App\Entity\Enrollment;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-demo-data',
    description: 'Loads demo data for Activities and Schedule',
)]
class LoadDemoDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Loading Demo Data...');

        // 1. Ensure School Cycle
        $cycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);
        if (!$cycle) {
            $cycle = new SchoolCycle();
            $cycle->setName('Ciclo 2025');
            $cycle->setStartDate(new \DateTime('2025-01-15'));
            $cycle->setEndDate(new \DateTime('2025-10-31'));
            $cycle->setIsActive(true);
            $this->entityManager->persist($cycle);
            $io->text('Created Active School Cycle.');
        }

        // 2. Load Activities
        $this->loadActivities($io);

        // 3. Load Schedule (and dependencies)
        $this->loadSchedule($cycle, $io);

        $this->entityManager->flush();

        $io->success('Demo data loaded successfully!');

        return Command::SUCCESS;
    }

    private function loadActivities(SymfonyStyle $io): void
    {
        $repo = $this->entityManager->getRepository(SchoolActivity::class);
        
        // Always add these if they don't exactly match to avoid dupes or just append?
        // Let's clear invalid or old demo ones if needed, but for safety let's just append or update.
        // The user specifically asked for these 4.
        
        $activities = [
            [
                'title' => 'Día de la Familia',
                'description' => 'Celebración anual con padres y alumnos.',
                'date' => new \DateTime('2025-05-15 09:00:00'),
                'location' => 'Campus Principal',
                'type' => 'EVENT',
                'audience' => 'ALL',
                'icon' => 'Users'
            ],
            [
                'title' => 'Campamento',
                'description' => 'Campamento anual de convivencia.',
                'date' => new \DateTime('2025-06-20 08:00:00'),
                'location' => 'Reserva Natural',
                'type' => 'TRIP',
                'audience' => 'ALL',
                'icon' => 'TrendingUp'
            ],
            [
                'title' => 'Día de la Independencia',
                'description' => 'Acto cívico y desfile.',
                'date' => new \DateTime('2025-09-15 08:00:00'),
                'location' => 'Plaza Cívica',
                'type' => 'EVENT',
                'audience' => 'ALL',
                'icon' => 'Award'
            ],
            [
                'title' => 'Clausura',
                'description' => 'Ceremonia de fin de ciclo escolar.',
                'date' => new \DateTime('2025-10-31 10:00:00'),
                'location' => 'Auditorio',
                'type' => 'EVENT',
                'audience' => 'ALL',
                'icon' => 'Award'
            ],
        ];

        foreach ($activities as $data) {
            // Check if exists to avoid dupes
            $exists = $repo->findOneBy(['title' => $data['title']]);
            if (!$exists) {
                $activity = new SchoolActivity();
                $activity->setTitle($data['title']);
                $activity->setDescription($data['description']);
                $activity->setDate($data['date']);
                $activity->setLocation($data['location']);
                $activity->setType($data['type']);
                $activity->setTargetAudience($data['audience']);
                $activity->setIcon($data['icon']);
                $this->entityManager->persist($activity);
            }
        }
        $io->text('Loaded requested activities: Día de la Familia, Campamento, Independencia, Clausura.');
    }

    private function loadSchedule(SchoolCycle $cycle, SymfonyStyle $io): void
    {
        $io->section('Loading Schedules');

        $teachers = $this->entityManager->getRepository(Teacher::class)->findAll();
        $subjects = $this->entityManager->getRepository(Subject::class)->findAll();
        
        // Obtener cursos (grados)
        $courses = $this->entityManager->getRepository(Course::class)->findAll();

        if (empty($teachers) || empty($subjects)) {
            $io->warning('Missing dependencies (Teachers, Subjects or Courses) for schedule. Skipping.');
            return;
        }

        // Mapear cursos por nombre para fácil acceso
        $courseMap = [];
        foreach ($courses as $course) {
            // Clave: "Nombre del Curso" (ej: "5to Primaria")
            // Como Course tiene gradeLevel y section, podemos construir una clave única o usar lógica de filtrado.
            // Por simplicidad, asumiremos que Course::getName() devuelve el nombre completo (ej: "1ro Básico").
            // Si hay múltiples secciones para el mismo grado, el nombre podría ser igual o diferente.
            // Agruparemos por nombre base.
            $key = $course->getName(); 
            if (!isset($courseMap[$key])) {
                $courseMap[$key] = [];
            }
            $courseMap[$key][] = $course;
        }

        $gradesList = [
            'Kinder', 'Preparatoria', 
            '1ro Primaria', '2do Primaria', '3ro Primaria', '4to Primaria', '5to Primaria', '6to Primaria',
            '1ro Básico', '2do Básico', '3ro Básico'
        ];
        
        $sections = ['A', 'B'];
        $schedulesCreated = 0;

        foreach ($gradesList as $gradeName) {
            foreach ($sections as $sectionName) {
                // Buscar el curso correspondiente
                $targetCourse = null;
                
                // Intento 1: Buscar en el mapa si existe el grado
                if (isset($courseMap[$gradeName])) {
                    foreach ($courseMap[$gradeName] as $c) {
                        // Si el curso ya tiene sección definida y coincide
                        if ($c->getSection() === $sectionName) {
                            $targetCourse = $c;
                            break;
                        }
                    }
                    // Intento 2: Si tienen cursos pero no coinciden sección, tomar el primero (fallback)
                    if (!$targetCourse && !empty($courseMap[$gradeName])) {
                         $targetCourse = $courseMap[$gradeName][0];
                    }
                }

                // Si no existe el curso, lo creamos "on the fly" para que el demo funcione
                if (!$targetCourse) {
                    $targetCourse = new Course();
                    $targetCourse->setName($gradeName);
                    $targetCourse->setGradeLevel($gradeName); // Simplificación
                    $targetCourse->setSection($sectionName);
                    $this->entityManager->persist($targetCourse);
                    // Actualizar mapa para futuras iteraciones
                    if (!isset($courseMap[$gradeName])) $courseMap[$gradeName] = [];
                    $courseMap[$gradeName][] = $targetCourse;
                }

                // Generar horario para Lunes a Viernes (1-5)
                for ($day = 1; $day <= 5; $day++) {
                    // 6 periodos por día
                    for ($period = 1; $period <= 6; $period++) {
                        // Probabilidad de tener clase (90%)
                        if (rand(1, 100) > 90) continue;

                        $schedule = new Schedule();
                        $schedule->setCourse($targetCourse); 
                        $schedule->setSchoolCycle($cycle);
                        $schedule->setTeacher($teachers[array_rand($teachers)]);
                        $schedule->setSubject($subjects[array_rand($subjects)]);
                        $schedule->setDayOfWeek($day);
                        $schedule->setPeriod($period);
                        
                        // Horas dummy
                        $startHour = 7 + $period; 
                        $schedule->setStartTime(new \DateTime(sprintf("%02d:00", $startHour)));
                        $schedule->setEndTime(new \DateTime(sprintf("%02d:45", $startHour)));
                        $schedule->setIsActive(true);

                        $this->entityManager->persist($schedule);
                        $schedulesCreated++;
                    }
                }
            }
        }

        $io->success("$schedulesCreated schedules created.");
    }
}
