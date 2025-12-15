<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'api_notifications_')]
class NotificationController extends AbstractController
{
    #[Route('/reset', name: 'reset', methods: ['DELETE'])]
    public function resetAll(): JsonResponse
    {
        // Logic to delete all notifications from database
        // $this->entityManager->createQuery('DELETE FROM App\Entity\Notification')->execute();
        
        return $this->json(['status' => 'All notifications deleted']);
    }
}
