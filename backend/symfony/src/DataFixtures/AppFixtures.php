<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Bimester;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 3. Create Users
        $users = [
            ['email' => 'admin@oxford.edu', 'role' => User::ROLE_SUPER_ADMIN, 'name' => 'Super Admin', 'password' => $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123'],
        ];


        foreach ($users as $userData) {
            $u = new User();
            $u->setEmail($userData['email']);
            $u->setRoles([$userData['role']]);
            $u->setName($userData['name']);
            // Hash password using the specific user instance
            // SECURITY: Use specific password if set, otherwise default
            $passToHash = $userData['password'] ?? $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123';
            $password = $this->passwordHasher->hashPassword($u, $passToHash);
            $u->setPassword($password); 
            $manager->persist($u);

        }

        $manager->flush();
    }
}
