<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:verify-login',
    description: 'Verifies user login credentials',
)]
class VerifyLoginCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User email')
             ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $io->note("Checking credentials for: $email");

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error('User not found.');
            return Command::FAILURE;
        }

        $io->text("User ID: " . $user->getId());
        $io->text("Roles: " . implode(', ', $user->getRoles()));
        $io->text("Stored Hash: " . $user->getPassword());

        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            $io->success('Password VALID.');
            return Command::SUCCESS;
        } else {
            $io->error('Password INVALID.');
            
            // Debug info
            $newHash = $this->passwordHasher->hashPassword($user, $password);
            $io->text("Expected hash for '$password': $newHash");
            
            return Command::FAILURE;
        }
    }
}
