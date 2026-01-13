<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'api_notifications_')]
class NotificationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'],
            20
        );

        $data = [];
        foreach ($notifications as $n) {
            $data[] = [
                'id' => $n->getId(),
                'title' => $n->getTitle(),
                'message' => $n->getMessage(),
                'read' => $n->isIsRead(),
                'time' => $n->getCreatedAt()->format('Y-m-d H:i'),
                'type' => 'message' // Default type as field doesn't exist yet
            ];
        }

        return $this->json($data);
    }

    #[Route('/{id}/read', name: 'mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        $user = $this->getUser();
        if ($notification->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/read-all', name: 'mark_all_read', methods: ['POST'])]
    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
             return $this->json(['error' => 'Not authenticated'], 401);
        }

        $this->entityManager->createQuery(
            'UPDATE App\Entity\Notification n SET n.isRead = true WHERE n.user = :user'
        )->setParameter('user', $user)->execute();

        return $this->json(['success' => true]);
    }

    #[Route('/reset', name: 'reset', methods: ['DELETE'])]
    public function resetAll(): JsonResponse
    {
        $user = $this->getUser();
         if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        // Only delete user's notifications
        $query = $this->entityManager->createQuery(
            'DELETE FROM App\Entity\Notification n WHERE n.user = :user'
        )->setParameter('user', $user);
        
        $deletedCount = $query->execute();
        
        return $this->json([
            'success' => true,
            'message' => "Se han eliminado $deletedCount notificaciones.",
            'count' => $deletedCount
        ]);
    }
    #[Route('/send', name: 'send', methods: ['POST'])]
    public function send(): JsonResponse
    {
        // Placeholder for send logic
        return $this->json(['message' => 'Notification queued'], 200);
    }
}
