<?php

namespace App\Controller;

use App\Entity\TeacherAvailability;
use App\Entity\ScheduleConstraint;
use App\Repository\TeacherAvailabilityRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/constraints')]
class ConstraintController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeacherAvailabilityRepository $availabilityRepository,
        private TeacherRepository $teacherRepository
    ) {}

    #[Route('/teacher-availability/{teacherId}', methods: ['GET'])]
    public function getTeacherAvailability(int $teacherId): JsonResponse
    {
        $availabilities = $this->availabilityRepository->findBy(['teacher' => $teacherId]);
        return $this->json($availabilities);
    }

    #[Route('/teacher-availability', methods: ['POST'])]
    public function setTeacherAvailability(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $teacher = $this->teacherRepository->find($data['teacherId']);

        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], 404);
        }

        $availability = new TeacherAvailability();
        $availability->setTeacher($teacher);
        $availability->setDayOfWeek($data['dayOfWeek']);
        $availability->setStartTime(new \DateTime($data['startTime']));
        $availability->setEndTime(new \DateTime($data['endTime']));
        $availability->setStatus($data['status']);

        $this->entityManager->persist($availability);
        $this->entityManager->flush();

        return $this->json(['status' => 'saved', 'id' => $availability->getId()]);
    }

    #[Route('/teacher-availability/{id}', methods: ['DELETE'])]
    public function deleteAvailability(int $id): JsonResponse
    {
        $availability = $this->availabilityRepository->find($id);
        if ($availability) {
            $this->entityManager->remove($availability);
            $this->entityManager->flush();
        }
        return $this->json(['status' => 'deleted']);
    }
}
