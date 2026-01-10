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
        $password = $this->passwordHasher->hashPassword(new User(), 'oxford123');

        $users = [
            ['email' => 'admin@oxford.edu', 'role' => User::ROLE_SUPER_ADMIN, 'name' => 'Super Admin'],
            // ['email' => 'director@oxford.edu', 'role' => User::ROLE_DIRECCION, 'name' => 'Director'],
            // ['email' => 'secretary@oxford.edu', 'role' => User::ROLE_SECRETARIA, 'name' => 'Secretaría'],
            // ['email' => 'accountant@oxford.edu', 'role' => User::ROLE_CONTABILIDAD, 'name' => 'Contabilidad'],
            // ['email' => 'coordination@oxford.edu', 'role' => User::ROLE_COORDINACION, 'name' => 'Coordinación'],
            // ['email' => 'informatics@oxford.edu', 'role' => User::ROLE_INFORMATICA, 'name' => 'Informática'],
        ];

        foreach ($users as $userData) {
            $u = new User();
            $u->setEmail($userData['email']);
            $u->setRoles([$userData['role']]);
            $u->setName($userData['name']);
            $u->setPassword($password); 
            $manager->persist($u);

        }

        $manager->flush();
    }
}
