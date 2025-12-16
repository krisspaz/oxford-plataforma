<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-users',
    description: 'Create test users for all roles'
)]
class SeedUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = [
            ['email' => 'admin@oxford.edu.gt', 'name' => 'Administrador Sistema', 'roles' => ['ROLE_ADMIN'], 'password' => 'admin123'],
            ['email' => 'secretaria@oxford.edu.gt', 'name' => 'María González', 'roles' => ['ROLE_SECRETARIA'], 'password' => 'secretaria123'],
            ['email' => 'contabilidad@oxford.edu.gt', 'name' => 'Roberto Pérez', 'roles' => ['ROLE_CONTABILIDAD'], 'password' => 'contabilidad123'],
            ['email' => 'coordinacion@oxford.edu.gt', 'name' => 'Ana Martínez', 'roles' => ['ROLE_COORDINACION'], 'password' => 'coordinacion123'],
            ['email' => 'docente@oxford.edu.gt', 'name' => 'Carlos Hernández', 'roles' => ['ROLE_DOCENTE'], 'password' => 'docente123'],
            ['email' => 'padre@oxford.edu.gt', 'name' => 'Juan López', 'roles' => ['ROLE_PADRE'], 'password' => 'padre123'],
            ['email' => 'estudiante@oxford.edu.gt', 'name' => 'Pedro Ramírez', 'roles' => ['ROLE_ALUMNO'], 'password' => 'estudiante123'],
            ['email' => 'direccion@oxford.edu.gt', 'name' => 'Laura Morales', 'roles' => ['ROLE_DIRECCION'], 'password' => 'direccion123'],
        ];

        foreach ($users as $userData) {
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
            
            if (!$existing) {
                $user = new User();
                $user->setEmail($userData['email']);
                $user->setName($userData['name']);
                $user->setRoles($userData['roles']);
                
                $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
                $user->setPassword($hashedPassword);
                
                $this->em->persist($user);
                $output->writeln("Created: {$userData['email']}");
            } else {
                $output->writeln("Exists: {$userData['email']}");
            }
        }

        $this->em->flush();
        $output->writeln("\n<info>Users seeded successfully!</info>");

        return Command::SUCCESS;
    }
}
