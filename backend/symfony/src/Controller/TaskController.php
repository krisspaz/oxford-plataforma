<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    #[Route('/my-tasks', methods: ['GET'])]
    public function getMyTasks(): JsonResponse
    {
        // In a real app, filtering by logged in user (Student or Teacher)
        // For now, return all tasks or a subset to ensure connection works
        // Assuming we might have a getUser() method if security is set up
        
        $user = $this->getUser();
        
        // Mock filtering logic if user is not fully set up in this dev phase
        // Just return latest tasks to prove connection
        $tasks = $this->taskRepository->findBy([], ['dueDate' => 'DESC'], 50);

        return $this->json($tasks, 200, [], ['groups' => ['task:read']]);
    }
}
