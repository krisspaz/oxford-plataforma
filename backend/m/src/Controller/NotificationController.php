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

    #[Route('/reset', name: 'reset', methods: ['DELETE'])]
    public function resetAll(): JsonResponse
    {
        // Delete all notifications
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Notification n');
        $deletedCount = $query->execute();
        
        return $this->json([
            'success' => true,
            'message' => "Se han eliminado $deletedCount notificaciones.",
            'count' => $deletedCount
        ]);
    }
}
