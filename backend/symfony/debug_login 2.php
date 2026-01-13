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
    // In Symfony 6+, this service is usually 'security.user_password_hasher' or alias
    // Since we are inside the app container, we should be able to get it if public,
    // or we use the alias 'Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface'
    if ($container->has(UserPasswordHasherInterface::class)) {
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
    } else {
        $passwordHasher = $container->get('security.user_password_hasher');
    }

    $email = 'teacher@oxford.edu';
    $password = 'oxford123';

    echo "Verifying $email / $password ...\n";

    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    if (!$user) {
        echo "User not found in DB!\n";
        return;
    }

    echo "User found. ID: " . $user->getId() . "\n";
    echo "Stored Hash: " . $user->getPassword() . "\n";

    $isValid = $passwordHasher->isPasswordValid($user, $password);

    if ($isValid) {
        echo "SUCCESS: Password is valid!\n";
    } else {
        echo "FAILURE: Password is INVALID.\n";
        // Try to see what it hashes to now
        $newHash = $passwordHasher->hashPassword($user, $password);
        echo "Expected hash for '$password' would look like: $newHash\n";
    }
};
