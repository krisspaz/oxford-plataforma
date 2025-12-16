<?php

namespace App\Controller;

use App\Repository\PermissionRepository;
use App\Repository\RolePermissionRepository;
use App\Entity\Permission;
use App\Entity\RolePermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/permissions')]
class PermissionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PermissionRepository $permissionRepository,
        private RolePermissionRepository $rolePermissionRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $grouped = $this->permissionRepository->findAllGroupedByModule();

        $result = [];
        foreach ($grouped as $module => $permissions) {
            $result[] = [
                'module' => $module,
                'permissions' => array_map(fn($p) => [
                    'id' => $p->getId(),
                    'code' => $p->getCode(),
                    'action' => $p->getAction(),
                    'description' => $p->getDescription(),
                ], $permissions)
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $result
        ]);
    }

    #[Route('/role/{role}', methods: ['GET'])]
    public function getByRole(string $role): JsonResponse
    {
        $permissions = $this->rolePermissionRepository->getPermissionCodesForRole($role);

        return $this->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    #[Route('/role/{role}', methods: ['POST'])]
    public function setRolePermissions(string $role, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $permissionCodes = $data['permissions'] ?? [];

        // Remove existing role permissions
        $existing = $this->rolePermissionRepository->findBy(['role' => $role]);
        foreach ($existing as $rp) {
            $this->em->remove($rp);
        }

        // Add new permissions
        foreach ($permissionCodes as $code) {
            $permission = $this->permissionRepository->findOneBy(['code' => $code]);
            if ($permission) {
                $rp = new RolePermission();
                $rp->setRole($role);
                $rp->setPermission($permission);
                $rp->setIsGranted(true);
                $this->em->persist($rp);
            }
        }

        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/check', methods: ['POST'])]
    public function check(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $role = $data['role'];
        $permission = $data['permission'];

        $hasPermission = $this->rolePermissionRepository->hasPermission($role, $permission);

        return $this->json([
            'success' => true,
            'hasPermission' => $hasPermission
        ]);
    }

    #[Route('/seed', methods: ['POST'])]
    public function seedPermissions(): JsonResponse
    {
        $modules = [
            'inscripciones' => ['view', 'create', 'edit', 'delete'],
            'pagos' => ['view', 'create', 'edit', 'delete', 'approve'],
            'notas' => ['view', 'create', 'edit', 'approve'],
            'bimestres' => ['view', 'create', 'edit', 'close', 'open'],
            'usuarios' => ['view', 'create', 'edit', 'delete'],
            'familias' => ['view', 'create', 'edit', 'delete'],
            'estudiantes' => ['view', 'create', 'edit', 'delete'],
            'paquetes' => ['view', 'create', 'edit', 'delete'],
            'materias' => ['view', 'create', 'edit', 'delete'],
            'docentes' => ['view', 'create', 'edit', 'delete'],
            'catalogos' => ['view', 'create', 'edit', 'delete'],
            'reportes' => ['view', 'export'],
            'configuracion' => ['view', 'edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $code = $module . '.' . $action;
                $existing = $this->permissionRepository->findOneBy(['code' => $code]);
                
                if (!$existing) {
                    $permission = new Permission();
                    $permission->setModule($module);
                    $permission->setAction($action);
                    $permission->setCode($code);
                    $permission->setDescription(ucfirst($action) . ' en módulo ' . ucfirst($module));
                    $this->em->persist($permission);
                }
            }
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Permissions seeded successfully'
        ]);
    }
}
