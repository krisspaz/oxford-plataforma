<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks', name: 'api_tasks_')]
class TaskController extends AbstractController
{
    private $taskRepository;
    private $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // In real app, filter by User Role/Course
        $tasks = $this->taskRepository->findAll();
        
        $data = array_map(function(Task $task) {
            return [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'start' => $task->getDueDate()->format('Y-m-d H:i:s'),
                'description' => $task->getDescription(),
                'course' => $task->getCourse()->getName(),
                'status' => $task->getStatus(),
            ];
        }, $tasks);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Simplified Logic
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? '');
        $task->setDueDate(new \DateTime($data['dueDate']));
        $task->setStatus('PENDING');
        // Associate Course logic skipped for MVP
        
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json(['status' => 'Task created'], 201);
    }
}
