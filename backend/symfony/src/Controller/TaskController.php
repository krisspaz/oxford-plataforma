<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskGrade;
use App\Entity\TaskSubmission;
use App\Entity\Teacher;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Bimester;
use App\Entity\Student;
use App\Entity\SchoolCycle;
use App\Repository\TaskRepository;
use App\Repository\TaskGradeRepository;
use App\Repository\TaskSubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Get tasks for a teacher
     */
    #[Route('', name: 'task_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $teacherId = $request->query->get('teacherId');
        $bimesterId = $request->query->get('bimesterId');
        $subjectId = $request->query->get('subjectId');
        $gradeId = $request->query->get('gradeId');
        $status = $request->query->get('status');

        $qb = $this->taskRepository->createQueryBuilder('t')
            ->leftJoin('t.subject', 's')
            ->leftJoin('t.teacher', 'te')
            ->leftJoin('t.bimester', 'b')
            ->leftJoin('t.taskGrades', 'tg')
            ->addSelect('s', 'te', 'b', 'tg')
            ->orderBy('t.dueDate', 'DESC');

        if ($teacherId) {
            $qb->andWhere('t.teacher = :teacherId')
               ->setParameter('teacherId', $teacherId);
        }

        if ($bimesterId) {
            $qb->andWhere('t.bimester = :bimesterId')
               ->setParameter('bimesterId', $bimesterId);
        }

        if ($subjectId) {
            $qb->andWhere('t.subject = :subjectId')
               ->setParameter('subjectId', $subjectId);
        }

        if ($gradeId) {
            $qb->andWhere('tg.grade = :gradeId')
               ->setParameter('gradeId', $gradeId);
        }

        if ($status) {
            $qb->andWhere('t.status = :status')
               ->setParameter('status', $status);
        }

        $tasks = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($t) => $this->serializeTask($t), $tasks));
    }

    /**
     * Get tasks for the current user (student or teacher)
     */
    #[Route('/my-tasks', name: 'task_student_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myTasks(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if user is a teacher
        $teacher = $this->em->getRepository(Teacher::class)->findOneBy(['user' => $user]);
        if ($teacher) {
            // Return teacher's assigned tasks
            $tasks = $this->taskRepository->findBy(['teacher' => $teacher], ['dueDate' => 'DESC']);
            return $this->json(array_map(fn($t) => $this->serializeTask($t), $tasks));
        }

        // Check if user is a student
        $student = $this->em->getRepository(Student::class)->findOneBy(['user' => $user]);
        if (!$student) {
            // Try finding by email fallback
            $student = $this->em->getRepository(Student::class)->findOneBy(['email' => $user->getEmail()]);
        }
        
        if (!$student) {
            return $this->json([
                'error' => 'Profile not found',
                'message' => 'No student or teacher profile linked to this account',
                'tasks' => []
            ]);
        }

        // Get enrollments to find Grade/Section
        $enrollments = $student->getEnrollments();
        if ($enrollments->isEmpty()) {
            return $this->json(['tasks' => [], 'message' => 'No active enrollment']);
        }

        $currentEnrollment = $enrollments->last();
        $grade = $currentEnrollment->getGrade();
        $section = $currentEnrollment->getSection();

        if (!$grade) {
            return $this->json(['tasks' => []]);
        }

        // Find tasks for this grade/section
        $tasks = $this->taskRepository->findForStudent($grade, $section);
        
        // Enrich with submission status
        $data = [];
        foreach ($tasks as $task) {
            $submission = $this->em->getRepository(TaskSubmission::class)->findOneBy(['task' => $task, 'student' => $student]);
            
            $taskData = $this->serializeTask($task);
            $taskData['mySubmission'] = $submission ? [
                'id' => $submission->getId(),
                'status' => $submission->getStatus(),
                'score' => $submission->getScore(),
                'submittedAt' => $submission->getSubmittedAt()?->format('Y-m-d H:i:s'),
                'isLate' => $submission->isLate(),
            ] : null;

            $data[] = $taskData;
        }

        return $this->json($data);
    }


    /**
     * Get a single task by ID
     */
    #[Route('/{id}', name: 'task_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeTask($task, true));
    }

    /**
     * Create a new task
     */
    #[Route('', name: 'task_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        if (!isset($data['title'], $data['dueDate'], $data['teacherId'], $data['subjectId'], $data['bimesterId'], $data['cycleId'], $data['grades'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $teacher = $this->em->getRepository(Teacher::class)->find($data['teacherId']);
        $subject = $this->em->getRepository(Subject::class)->find($data['subjectId']);
        $bimester = $this->em->getRepository(Bimester::class)->find($data['bimesterId']);
        $cycle = $this->em->getRepository(SchoolCycle::class)->find($data['cycleId']);

        if (!$teacher || !$subject || !$bimester || !$cycle) {
            return $this->json(['error' => 'Invalid references'], Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? null);
        $task->setDueDate(new \DateTime($data['dueDate']));
        $task->setType($data['type'] ?? Task::TYPE_HOMEWORK);
        $task->setPoints($data['points'] ?? 10);
        $task->setStatus(Task::STATUS_ACTIVE);
        $task->setTeacher($teacher);
        $task->setSubject($subject);
        $task->setBimester($bimester);
        $task->setSchoolCycle($cycle);

        $this->em->persist($task);

        // Add grades
        foreach ($data['grades'] as $gradeData) {
            $grade = $this->em->getRepository(Grade::class)->find($gradeData['gradeId']);
            if ($grade) {
                $taskGrade = new TaskGrade();
                $taskGrade->setTask($task);
                $taskGrade->setGrade($grade);
                
                if (isset($gradeData['sectionId'])) {
                    $section = $this->em->getRepository(Section::class)->find($gradeData['sectionId']);
                    $taskGrade->setSection($section);
                }
                
                $this->em->persist($taskGrade);
            }
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $task->getId(),
            'message' => 'Tarea creada correctamente'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a task
     */
    #[Route('/{id}', name: 'task_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) $task->setTitle($data['title']);
        if (isset($data['description'])) $task->setDescription($data['description']);
        if (isset($data['dueDate'])) $task->setDueDate(new \DateTime($data['dueDate']));
        if (isset($data['type'])) $task->setType($data['type']);
        if (isset($data['points'])) $task->setPoints($data['points']);
        if (isset($data['status'])) $task->setStatus($data['status']);

        // Update grades if provided
        if (isset($data['grades'])) {
            // Remove existing grades
            foreach ($task->getTaskGrades() as $tg) {
                $this->em->remove($tg);
            }

            // Add new grades
            foreach ($data['grades'] as $gradeData) {
                $grade = $this->em->getRepository(Grade::class)->find($gradeData['gradeId']);
                if ($grade) {
                    $taskGrade = new TaskGrade();
                    $taskGrade->setTask($task);
                    $taskGrade->setGrade($grade);
                    
                    if (isset($gradeData['sectionId'])) {
                        $section = $this->em->getRepository(Section::class)->find($gradeData['sectionId']);
                        $taskGrade->setSection($section);
                    }
                    
                    $this->em->persist($taskGrade);
                }
            }
        }

        $task->setUpdatedAt(new \DateTime());
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Tarea actualizada correctamente'
        ]);
    }

    /**
     * Delete a task
     */
    #[Route('/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($task);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Tarea eliminada']);
    }

    /**
     * Get submissions for a task
     */
    #[Route('/{id}/submissions', name: 'task_submissions', methods: ['GET'])]
    public function getSubmissions(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $submissions = $this->em->getRepository(TaskSubmission::class)
            ->findBy(['task' => $task], ['submittedAt' => 'DESC']);

        $data = array_map(fn($s) => [
            'id' => $s->getId(),
            'studentId' => $s->getStudent()->getId(),
            'studentName' => $s->getStudent()->getFullName(),
            'studentCode' => $s->getStudent()->getStudentCode(),
            'submittedAt' => $s->getSubmittedAt()->format('Y-m-d H:i:s'),
            'status' => $s->getStatus(),
            'score' => $s->getScore(),
            'feedback' => $s->getFeedback(),
            'isLate' => $s->isLate(),
        ], $submissions);

        return $this->json($data);
    }

    /**
     * Submit a task (for students)
     */
    #[Route('/{id}/submit', name: 'task_submit', methods: ['POST'])]
    public function submit(int $id, Request $request): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $student = $this->em->getRepository(Student::class)->find($data['studentId']);

        if (!$student) {
            return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if already submitted
        $existing = $this->em->getRepository(TaskSubmission::class)
            ->findOneBy(['task' => $task, 'student' => $student]);

        if ($existing) {
            return $this->json(['error' => 'Ya has entregado esta tarea'], Response::HTTP_BAD_REQUEST);
        }

        $submission = new TaskSubmission();
        $submission->setTask($task);
        $submission->setStudent($student);
        $submission->setContent($data['content'] ?? null);
        $submission->setAttachmentUrl($data['attachmentUrl'] ?? null);

        if ($submission->isLate()) {
            $submission->setStatus(TaskSubmission::STATUS_LATE);
        }

        $this->em->persist($submission);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $submission->getId(),
            'message' => 'Tarea entregada correctamente'
        ], Response::HTTP_CREATED);
    }

    /**
     * Grade a submission
     */
    #[Route('/submissions/{submissionId}/grade', name: 'task_grade_submission', methods: ['POST'])]
    public function gradeSubmission(int $submissionId, Request $request): JsonResponse
    {
        $submission = $this->em->getRepository(TaskSubmission::class)->find($submissionId);
        if (!$submission) {
            return $this->json(['error' => 'Submission not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $submission->setScore($data['score']);
        $submission->setFeedback($data['feedback'] ?? null);
        $submission->setStatus(TaskSubmission::STATUS_GRADED);
        $submission->setGradedAt(new \DateTime());

        if (isset($data['gradedById'])) {
            $teacher = $this->em->getRepository(Teacher::class)->find($data['gradedById']);
            $submission->setGradedBy($teacher);
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Calificación guardada'
        ]);
    }

    private function serializeTask(Task $task, bool $detailed = false): array
    {
        $data = [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'type' => $task->getType(),
            'status' => $task->getStatus(),
            'points' => $task->getPoints(),
            'dueDate' => $task->getDueDate()->format('Y-m-d'),
            'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
            'subject' => $task->getSubject() ? [
                'id' => $task->getSubject()->getId(),
                'name' => $task->getSubject()->getName(),
            ] : null,
            'teacher' => $task->getTeacher() ? [
                'id' => $task->getTeacher()->getId(),
                'name' => $task->getTeacher()->getFullName(),
            ] : null,
            'bimester' => $task->getBimester() ? [
                'id' => $task->getBimester()->getId(),
                'name' => $task->getBimester()->getName(),
            ] : null,
            'grades' => array_map(fn($tg) => [
                'gradeId' => $tg->getGrade()->getId(),
                'gradeName' => $tg->getGrade()->getName(),
                'sectionId' => $tg->getSection()?->getId(),
                'sectionName' => $tg->getSection()?->getName(),
            ], $task->getTaskGrades()->toArray()),
            'submissionCount' => $task->getSubmissionCount(),
            'pendingCount' => $task->getPendingCount(),
        ];

        if ($detailed) {
            $data['submissions'] = array_map(fn($s) => [
                'id' => $s->getId(),
                'studentId' => $s->getStudent()->getId(),
                'studentName' => $s->getStudent()->getFullName(),
                'status' => $s->getStatus(),
                'score' => $s->getScore(),
            ], $task->getSubmissions()->toArray());
        }

        return $data;
    }
}
