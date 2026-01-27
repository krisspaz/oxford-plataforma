<?php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

use App\Entity\User;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();
// $passwordHasher = $container->get('security.user_password_hasher');

$email = 'admin@oxford.edu';
$repository = $em->getRepository(User::class);
$existingUser = $repository->findOneBy(['email' => $email]);

if ($existingUser) {
    echo "User $email already exists. Resetting password...\n";
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $existingUser->setPassword($hashedPassword);
    $existingUser->setIsActive(true);
    $existingUser->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
    $em->flush();
    echo "Password reset to: admin123\n";
    exit(0);
}

echo "Creating user $email...\n";

$user = new User();
$user->setEmail($email);
$user->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
$user->setIsActive(true);
$user->setName('Super Admin');
$user->setTwoFactorAuthEnabled(false);

// Hash password manually (bcrypt is compatible with Symfony auto)
$hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
$user->setPassword($hashedPassword);

$em->persist($user);

// Create Person profile
$person = new Person();
$person->setFirstName('Super');
$person->setLastName('Admin');
$person->setIsActive(true);
$person->setCreatedAt(new \DateTime());
$person->setUser($user);
$person->setPersonType('staff');

$em->persist($person);
$em->flush();

echo "User created successfully with password: admin123\n";
