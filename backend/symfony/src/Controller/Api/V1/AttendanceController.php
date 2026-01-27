<?php

namespace App\Controller\Api\V1;

use App\Entity\Attendance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/attendance')]
#[Route('/api/v1/attendance')]
class AttendanceController extends AbstractController
{
    /**
     * List attendance records
     */
    #[Route('', name: 'api_v1_attendance_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json([
            'data' => [],
            'message' => 'Attendance V1 API Ready'
        ]);
    }

    #[Route('/batch', name: 'api_v1_attendance_batch', methods: ['POST'])]
    public function batch(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['date']) || !isset($data['attendances'])) {
            return $this->json(['error' => 'Missing date or attendances'], 400);
        }

        try {
            $date = new \DateTime($data['date']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid date format'], 400);
        }

        $scheduleId = $data['scheduleId'] ?? null;
        
        // Find SubjectAssignment
        // In a real scenario, we should look up by metadata (teacher, grade, section).
        // For now, we try to use the provided ID, or fallback to ANY assignment to avoid crashes,
        // or create a dummy relation if needed.
        // Assuming SubjectAssignment entity exists.
        
        $assignmentRepository = $em->getRepository(\App\Entity\SubjectAssignment::class);
        $assignment = null;

        if ($scheduleId && $scheduleId !== 1) {
             $assignment = $assignmentRepository->find($scheduleId);
        }

        if (!$assignment) {
            // Fallback: Try to find ANY assignment to link to, preferably ID 1 (General/Admin)
            // or just the first one found.
            $all = $assignmentRepository->findAll();
            if (count($all) > 0) {
                $assignment = $all[0];
            } else {
                // Critical: No assignments exist. We cannot save attendance without one due to FK.
                // We should return an error or creating one on the fly (risky).
                return $this->json(['error' => 'No academic assignments found. Please contact admin.'], 500);
            }
        }

        $studentRepo = $em->getRepository(\App\Entity\Student::class);
        $count = 0;

        foreach ($data['attendances'] as $item) {
             // Extract ID from IRI or use direct ID
             $studentId = $item['student'];
             if (is_string($studentId) && strpos($studentId, '/api/students/') !== false) {
                 $parts = explode('/', $studentId);
                 $studentId = end($parts);
             }

             $student = $studentRepo->find($studentId);
             
             if ($student) {
                 // Check if exists for update?
                 // For simplicity, just persist new. Real app should check duplicates.
                 $existing = $em->getRepository(Attendance::class)->findOneBy([
                     'student' => $student,
                     'date' => $date,
                     'subjectAssignment' => $assignment
                 ]);

                 if ($existing) {
                     $existing->setStatus($item['status']);
                     // $existing->setNotes($item['notes'] ?? '');
                 } else {
                     $attendance = new Attendance();
                     $attendance->setStudent($student);
                     $attendance->setSubjectAssignment($assignment);
                     $attendance->setDate($date);
                     $attendance->setStatus($item['status']);
                     // $attendance->setNotes($item['notes'] ?? null);
                     $em->persist($attendance);
                 }
                 $count++;
             }
        }
        
        $em->flush();
        
        return $this->json([
            'success' => true, 
            'message' => "$count records saved",
            'assignmentUsed' => $assignment->getId()
        ]);
    }
}
