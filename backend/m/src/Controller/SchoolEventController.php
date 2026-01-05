<?php

namespace App\Controller;

use App\Entity\SchoolActivity;
use App\Repository\SchoolActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/events')]
class SchoolEventController extends AbstractController
{
    #[Route('', name: 'get_school_events', methods: ['GET'])]
    public function getEvents(SchoolActivityRepository $repo): JsonResponse
    {
        // For production, filter by date range or Active status
        $events = $repo->findBy(['isActive' => true], ['date' => 'ASC']);
        
        $data = [];
        foreach ($events as $e) {
            $data[] = [
                'id' => $e->getId(),
                'title' => $e->getTitle(),
                'description' => $e->getDescription(),
                'date' => $e->getDate()->format('c'),
                'type' => $e->getType(),
                'location' => $e->getLocation(),
                'target' => $e->getTargetAudience(),
                'icon' => $e->getIcon()
            ];
        }

        return $this->json($data);
    }

    #[Route('/init', name: 'init_dummy_events', methods: ['POST'])]
    public function initEvents(EntityManagerInterface $em): JsonResponse
    {
        // Helper to seed dummy events if empty
        $existing = $em->getRepository(SchoolActivity::class)->count([]);
        if ($existing > 0) return $this->json(['message' => 'Events already exist']);

        $events = [
            ['title' => 'Inicio de Clases', 'date' => '+2 days', 'type' => 'EVENT', 'icon' => 'School'],
            ['title' => 'Feria Científica', 'date' => '+15 days', 'type' => 'EVENT', 'icon' => 'FlaskConical'],
            ['title' => 'Entrega de Boletas', 'date' => '+30 days', 'type' => 'MEETING', 'icon' => 'FileText'],
            ['title' => 'Día de la Familia', 'date' => '+45 days', 'type' => 'EVENT', 'icon' => 'Users'],
        ];

        foreach ($events as $evt) {
            $e = new SchoolActivity();
            $e->setTitle($evt['title']);
            $e->setDate(new \DateTime($evt['date']));
            $e->setType($evt['type']);
            $e->setIcon($evt['icon']);
            $e->setTargetAudience('ALL');
            $e->setDescription('Evento oficial del colegio.');
            $e->setLocation('Campus Principal');
            $em->persist($e);
        }
        $em->flush();

        return $this->json(['success' => true]);
    }
}
