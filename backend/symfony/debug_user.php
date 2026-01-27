<?php
require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();
$repository = $em->getRepository(User::class);

$email = 'admin@oxford.edu';
$inputPassword = 'admin123';

echo "--- Debugging User Login ---\n";
echo "Attempting to find user: $email\n";

$user = $repository->findOneBy(['email' => $email]);

if (!$user) {
    echo "ERROR: User not found in database!\n";
    exit(1);
}

echo "User found! ID: " . $user->getId() . "\n";
echo "Stored Hash: " . $user->getPassword() . "\n";
echo "Roles: " . implode(', ', $user->getRoles()) . "\n";
echo "Active: " . ($user->getIsActive() ? 'YES' : 'NO') . "\n";

echo "--- Verifying Password ---\n";
$isMatch = password_verify($inputPassword, $user->getPassword());

if ($isMatch) {
    echo "SUCCESS: Password '$inputPassword' matches the stored hash.\n";
} else {
    echo "FAILURE: Password '$inputPassword' DOES NOT match the stored hash.\n";
    echo "Re-hashing '$inputPassword' with BCRYPT gives: " . password_hash($inputPassword, PASSWORD_BCRYPT) . "\n";
}
