<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    // Generic CRUD removed in favor of API Platform standard implementation

    #[Route('/{id}/status', methods: ['PATCH'])]
    public function toggleStatus(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }
        
        $user->setIsActive(!$user->getIsActive());
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeUser($user),
            'message' => $user->getIsActive() ? 'Usuario activado' : 'Usuario desactivado'
        ]);
    }

    #[Route('/{id}/password', methods: ['PATCH'])]
    public function updatePassword(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['password'])) {
            return $this->json(['success' => false, 'error' => 'Contraseña requerida'], 400);
        }
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    #[Route('/roles', methods: ['GET'])]
    public function roles(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => User::getAvailableRoles()
        ]);
    }

    private function serializeUser(User $u): array
    {
        return [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'name' => $u->getName(),
            'roles' => $u->getRoles(),
            'isActive' => $u->getIsActive(),
            'lastLogin' => null, // TODO: Add lastLogin field
        ];
    }
}
