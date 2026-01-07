<?php

namespace App\Command;

use App\Entity\Bimester;
use App\Entity\SchoolCycle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-bimesters',
    description: 'Seeds bimesters for the current school cycle',
)]
class SeedBimestersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check if bimesters already exist
        $existingBimesters = $this->entityManager->getRepository(Bimester::class)->findAll();
        if (count($existingBimesters) > 0) {
            $io->note("Bimesters already exist: " . count($existingBimesters) . " found.");
            foreach ($existingBimesters as $b) {
                $io->text(" - " . $b->getName());
            }
            return Command::SUCCESS;
        }

        // Get or create a school cycle
        $schoolCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);
        if (!$schoolCycle) {
            // Create a default school cycle
            $schoolCycle = new SchoolCycle();
            $schoolCycle->setName('Ciclo Escolar 2025');
            $schoolCycle->setStartDate(new \DateTime('2025-01-06'));
            $schoolCycle->setEndDate(new \DateTime('2025-10-31'));
            $schoolCycle->setIsActive(true);
            $this->entityManager->persist($schoolCycle);
            $io->note("Created school cycle: Ciclo Escolar 2025");
        }

        // Create 4 bimesters
        $bimestersData = [
            ['name' => '1er Bimestre', 'number' => 1, 'start' => '2025-01-06', 'end' => '2025-03-14'],
            ['name' => '2do Bimestre', 'number' => 2, 'start' => '2025-03-17', 'end' => '2025-05-30'],
            ['name' => '3er Bimestre', 'number' => 3, 'start' => '2025-06-02', 'end' => '2025-08-15'],
            ['name' => '4to Bimestre', 'number' => 4, 'start' => '2025-08-18', 'end' => '2025-10-31'],
        ];

        foreach ($bimestersData as $data) {
            $bimester = new Bimester();
            $bimester->setName($data['name']);
            $bimester->setNumber($data['number']);
            $bimester->setStartDate(new \DateTime($data['start']));
            $bimester->setEndDate(new \DateTime($data['end']));
            $bimester->setSchoolCycle($schoolCycle);
            
            $this->entityManager->persist($bimester);
            $io->text("Created: {$data['name']}");
        }

        $this->entityManager->flush();

        $io->success('4 bimesters created successfully!');

        return Command::SUCCESS;
    }
}
