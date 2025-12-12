<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin User
        $user = new User();
        $user->setEmail('admin@oxford.edu');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'oxford123');
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();
    }
}
