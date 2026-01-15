<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:restore-users',
    description: 'Restores default users with password oxford123',
)]
class RestoreUsersCommand extends Command
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

        $users = [
            ['email' => 'admin@oxford.edu', 'role' => 'ROLE_SUPER_ADMIN', 'name' => 'Super Admin'],
            ['email' => 'director@oxford.edu', 'role' => 'ROLE_DIRECTOR', 'name' => 'Director'],
            ['email' => 'secretary@oxford.edu', 'role' => 'ROLE_SECRETARY', 'name' => 'Secretaria'],
            ['email' => 'accountant@oxford.edu', 'role' => 'ROLE_ACCOUNTANT', 'name' => 'Contabilidad'],
            ['email' => 'coordination@oxford.edu', 'role' => 'ROLE_COORDINATION', 'name' => 'Coordinacion'],
            ['email' => 'informatics@oxford.edu', 'role' => 'ROLE_INFORMATICS', 'name' => 'Informatica'],
            ['email' => 'teacher@oxford.edu', 'role' => 'ROLE_TEACHER', 'name' => 'Docente'],
            ['email' => 'student@oxford.edu', 'role' => 'ROLE_STUDENT', 'name' => 'Estudiante'],
            ['email' => 'parent@oxford.edu', 'role' => 'ROLE_PARENT', 'name' => 'Padre'],
        ];

        foreach ($users as $userData) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);

            if (!$user) {
                $user = new User();
                $user->setEmail($userData['email']);
                $io->note("Creating {$userData['email']}...");
            } else {
                $io->note("Updating {$userData['email']}...");
            }

            $user->setRoles([$userData['role']]);
            // Hash password 'oxford123'
            // SECURITY: Use environment variable or fallback for dev
            $defaultPass = $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123';
            $hashedPassword = $this->passwordHasher->hashPassword($user, $defaultPass);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $io->success('All users have been restored with the default password!');

        return Command::SUCCESS;
    }
}
