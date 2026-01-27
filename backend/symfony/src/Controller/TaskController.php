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

    #[Route('', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em): JsonResponse
    {
        // ... (methods implementation kept but ensuring singular class closing)
        $data = json_decode($request->getContent(), true);
        
        $task = new \App\Entity\Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? '');
        $task->setDueDate(new \DateTime($data['dueDate']));
        $task->setPoints($data['points'] ?? 10);
        $task->setType($data['type'] ?? 'tarea');
        $task->setStatus('active');
        
        // Link Teacher
        if (isset($data['teacherId'])) {
            $teacher = $em->getRepository(\App\Entity\Teacher::class)->find($data['teacherId']);
            if ($teacher) $task->setCreatedBy($teacher); 
        }
        
        // Link Subject
        if (isset($data['subjectId'])) {
            $subject = $em->getRepository(\App\Entity\Subject::class)->find($data['subjectId']);
            if ($subject) $task->setSubject($subject);
        }
        
        // Link Bimester
        if (isset($data['bimesterId'])) {
             $bimester = $em->getRepository(\App\Entity\Bimester::class)->find($data['bimesterId']);
             if ($bimester) $task->setBimester($bimester);
        }

        $task->setCycle('2025');

        $em->persist($task);
        $em->flush();

        return $this->json($task, 201, [], ['groups' => ['task:read']]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, \Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) return $this->json(['error' => 'Task not found'], 404);

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) $task->setTitle($data['title']);
        if (isset($data['description'])) $task->setDescription($data['description']);
        if (isset($data['dueDate'])) $task->setDueDate(new \DateTime($data['dueDate']));
        if (isset($data['points'])) $task->setPoints($data['points']);
        if (isset($data['status'])) $task->setStatus($data['status']);

        $em->flush();

        return $this->json($task, 200, [], ['groups' => ['task:read']]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, \Doctrine\ORM\EntityManagerInterface $em): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) return $this->json(['error' => 'Task not found'], 404);

        $em->remove($task);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
