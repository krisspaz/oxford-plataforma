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

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($u) => $this->serializeUser($u), $users)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeUser($user)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Check if email exists
        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            return $this->json(['success' => false, 'error' => 'El email ya está en uso'], 400);
        }
        
        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name'] ?? null);
        $user->setRoles([$data['role'] ?? 'ROLE_USER']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password'] ?? 'oxford2025');
        $user->setPassword($hashedPassword);
        
        $this->em->persist($user);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeUser($user)
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) $user->setName($data['name']);
        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['role'])) $user->setRoles([$data['role']]);
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeUser($user)
        ]);
    }

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
