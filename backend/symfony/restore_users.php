<?php

use App\Entity\User;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

require_once __DIR__ . '/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();
    $entityManager = $container->get('doctrine')->getManager();
    $passwordHasher = $container->get('security.user_password_hasher');

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

    echo "Restoring users...\n";

    foreach ($users as $userData) {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);

        if (!$user) {
            $user = new User();
            $user->setEmail($userData['email']);
            echo "Creating {$userData['email']}...\n";
        } else {
            echo "Updating {$userData['email']}...\n";
        }

        $user->setRoles([$userData['role']]);
        
        // Hash password 'oxford123'
        $hashedPassword = $passwordHasher->hashPassword($user, 'oxford123');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
    }

    $entityManager->flush();
    echo "Done! All users have password 'oxford123'.\n";
};
