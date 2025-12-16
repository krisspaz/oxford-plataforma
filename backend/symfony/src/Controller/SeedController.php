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
                'email' => 'admin@oxford.edu.gt',
                'name' => 'Administrador Sistema',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'admin123'
            ],
            [
                'email' => 'secretaria@oxford.edu.gt',
                'name' => 'María González',
                'roles' => ['ROLE_SECRETARIA'],
                'password' => 'secretaria123'
            ],
            [
                'email' => 'contabilidad@oxford.edu.gt',
                'name' => 'Roberto Pérez',
                'roles' => ['ROLE_CONTABILIDAD'],
                'password' => 'contabilidad123'
            ],
            [
                'email' => 'coordinacion@oxford.edu.gt',
                'name' => 'Ana Martínez',
                'roles' => ['ROLE_COORDINACION'],
                'password' => 'coordinacion123'
            ],
            [
                'email' => 'docente@oxford.edu.gt',
                'name' => 'Carlos Hernández',
                'roles' => ['ROLE_DOCENTE'],
                'password' => 'docente123'
            ],
            [
                'email' => 'padre@oxford.edu.gt',
                'name' => 'Juan López',
                'roles' => ['ROLE_PADRE'],
                'password' => 'padre123'
            ],
            [
                'email' => 'estudiante@oxford.edu.gt',
                'name' => 'Pedro Ramírez',
                'roles' => ['ROLE_ALUMNO'],
                'password' => 'estudiante123'
            ],
            [
                'email' => 'direccion@oxford.edu.gt',
                'name' => 'Laura Morales',
                'roles' => ['ROLE_DIRECCION'],
                'password' => 'direccion123'
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
