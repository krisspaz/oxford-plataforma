<?php

namespace App\Command;

use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-teacher',
    description: 'Creates Teacher entity linked to teacher@oxford.edu user',
)]
class CreateTeacherCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = 'teacher@oxford.edu';

        // Check if user exists
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error("User $email not found. Run app:restore-users first.");
            return Command::FAILURE;
        }

        // Check if teacher already exists
        $existingTeacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['email' => $email]);
        if ($existingTeacher) {
            $io->note("Teacher with email $email already exists (ID: {$existingTeacher->getId()})");
            return Command::SUCCESS;
        }

        // Create Teacher entity
        $teacher = new Teacher();
        $teacher->setFirstName('Profesor');
        $teacher->setLastName('Demo');
        $teacher->setEmail($email);
        $teacher->setPhone('55555555');
        $teacher->setHireDate(new \DateTime('2020-01-15'));
        
        // Check if setIsActive exists
        if (method_exists($teacher, 'setIsActive')) {
            $teacher->setIsActive(true);
        }

        $this->entityManager->persist($teacher);
        $this->entityManager->flush();

        $io->success("Teacher created successfully with ID: {$teacher->getId()}");

        return Command::SUCCESS;
    }
}
