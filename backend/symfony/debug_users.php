<?php
// debug_users.php
require 'vendor/autoload.php';

use App\Kernel;
use App\Entity\User;
use App\Entity\Teacher;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();

echo "--- USERS ---\n";
$users = $em->getRepository(User::class)->findAll();
foreach ($users as $u) {
    echo "ID: " . $u->getId() . " | Email: " . $u->getEmail() . " | Roles: " . implode(',', $u->getRoles()) . "\n";
}

echo "\n--- TEACHERS ---\n";
$teachers = $em->getRepository(Teacher::class)->findAll();
foreach ($teachers as $t) {
    echo "ID: " . $t->getId() . " | Email: " . $t->getEmail() . " | Name: " . $t->getFirstName() . "\n";
}
