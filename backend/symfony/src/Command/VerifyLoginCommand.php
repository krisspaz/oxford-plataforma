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
    name: 'app:verify-login',
    description: 'Verifies login for a user',
)]
class VerifyLoginCommand extends Command
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

        $email = 'teacher@oxford.edu';
        $password = 'oxford123';

        $io->note("Verifying $email / $password ...");

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error("User not found in DB!");
            return Command::FAILURE;
        }

        $io->note("User found. ID: " . $user->getId());
        $io->note("Stored Hash: " . $user->getPassword());

        $isValid = $this->passwordHasher->isPasswordValid($user, $password);

        if ($isValid) {
            $io->success("SUCCESS: Password is valid!");
        } else {
            $io->error("FAILURE: Password is INVALID.");
            $newHash = $this->passwordHasher->hashPassword($user, $password);
            $io->note("Expected hash for '$password' would look like: $newHash");
        }

        return Command::SUCCESS;
    }
}
