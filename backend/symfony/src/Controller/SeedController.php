<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/seed')]
class SeedController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/users', methods: ['POST'])]
    public function seedUsers(): JsonResponse
    {
        $users = [
            [
                'email' => 'admin@oxford.edu',
                'name' => 'Administrador Sistema',
                'roles' => [
                    'ROLE_ADMIN', 
                    'ROLE_DIRECTOR', 
                    'ROLE_COORDINATION', 
                    'ROLE_SECRETARY', 
                    'ROLE_ACCOUNTANT', 
                    'ROLE_INFORMATICS',
                    'ROLE_TEACHER',
                    'ROLE_STUDENT',
                    'ROLE_PARENT'
                ],
                'password' => 'oxford123'
            ],
            [
                'email' => 'secretary@oxford.edu',
                'name' => 'María González',
                'roles' => ['ROLE_SECRETARY'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'accountant@oxford.edu',
                'name' => 'Roberto Pérez',
                'roles' => ['ROLE_ACCOUNTANT'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'coordination@oxford.edu',
                'name' => 'Ana Martínez',
                'roles' => ['ROLE_COORDINATION'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'teacher@oxford.edu',
                'name' => 'Carlos Hernández',
                'roles' => ['ROLE_TEACHER'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'parent@oxford.edu',
                'name' => 'Juan López',
                'roles' => ['ROLE_PARENT'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'student@oxford.edu',
                'name' => 'Pedro Ramírez',
                'roles' => ['ROLE_STUDENT'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'director@oxford.edu',
                'name' => 'Laura Morales',
                'roles' => ['ROLE_DIRECTOR'],
                'password' => 'oxford123'
            ],
            [
                'email' => 'informatics@oxford.edu',
                'name' => 'Soporte TI',
                'roles' => ['ROLE_INFORMATICS'],
                'password' => 'oxford123'
            ],
        ];

        $created = [];

        foreach ($users as $userData) {
            // Check if user exists
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
            
            if (!$existing) {
                $user = new User();
                $user->setEmail($userData['email']);
                $user->setName($userData['name']);
                $user->setRoles($userData['roles']);
                $user->setIsActive(true);
                
                $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
                $user->setPassword($hashedPassword);
                
                $this->em->persist($user);
                $created[] = $userData['email'];
            }
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Users seeded successfully',
            'created' => $created
        ]);
    }

    #[Route('/all', methods: ['POST'])]
    public function seedAll(): JsonResponse
    {
        // Seed users
        $usersResult = $this->seedUsers();
        
        // Can add more seed methods here
        
        return $this->json([
            'success' => true,
            'message' => 'All data seeded successfully'
        ]);
    }
}
