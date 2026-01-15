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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-teachers-from-images',
    description: 'Seeds teachers identified in user screenshots',
)]
class SeedTeachersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seeding Teachers from Images');

        $teachers = [
            ['name' => 'Miss Eneyda', 'email' => 'eneyda@oxford.edu'],
            ['name' => 'Miss Grethel', 'email' => 'grethel@oxford.edu'],
            ['name' => 'Miss Wendy', 'email' => 'wendy@oxford.edu'],
            ['name' => 'Miss Anabela', 'email' => 'anabela@oxford.edu'],
        ];

        foreach ($teachers as $data) {
            $existing = $this->entityManager->getRepository(Teacher::class)->findOneBy(['email' => $data['email']]);
            if (!$existing) {
                // Create User first
                $user = new User();
                $user->setName($data['name']);
                $user->setEmail($data['email']);
                $user->setRoles([User::ROLE_DOCENTE]);
                $user->setPassword($this->passwordHasher->hashPassword($user, 'oxford123'));
                $this->entityManager->persist($user);

                // Create Teacher
                $teacher = new Teacher();
                $teacher->setUser($user);
                $teacher->setFirstName(explode(' ', $data['name'])[0]); // Simple split
                $teacher->setLastName(explode(' ', $data['name'])[1] ?? ''); // Simple split
                $teacher->setEmail($data['email']);
                $teacher->setPhone('00000000');
                $teacher->setAddress('Oxford System');
                $this->entityManager->persist($teacher);
                
                $io->text("Created Teacher: {$data['name']}");
            }
        }

        $this->entityManager->flush();
        $io->success('Teachers seeded successfully.');

        return Command::SUCCESS;
    }
}
