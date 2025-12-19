<?php

namespace App\Controller;

use App\Entity\SchoolActivity;
use App\Repository\SchoolActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/school-activities')]
class SchoolActivityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SchoolActivityRepository $schoolActivityRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        // Optional filters
        $limit = $request->query->get('limit', 10);
        $audience = $request->query->get('audience'); // ALL, STUDENTS, PARENTS
        
        $qb = $this->schoolActivityRepository->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->andWhere('s.date >= :today')
            ->setParameter('active', true)
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit);

        if ($audience) {
            $qb->andWhere('s.targetAudience IN (:audiences)')
               ->setParameter('audiences', ['ALL', $audience]);
        }

        $activities = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($a) => [
            'id' => $a->getId(),
            'title' => $a->getTitle(),
            'description' => $a->getDescription(),
            'date' => $a->getDate()->format('Y-m-d H:i:s'),
            'location' => $a->getLocation(),
            'type' => $a->getType(),
            'icon' => $a->getIcon(),
            'audience' => $a->getTargetAudience()
        ], $activities));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Uncomment in prod

        $data = json_decode($request->getContent(), true);

        $activity = new SchoolActivity();
        $activity->setTitle($data['title']);
        $activity->setDescription($data['description'] ?? null);
        $activity->setDate(new \DateTime($data['date']));
        $activity->setLocation($data['location'] ?? null);
        $activity->setType($data['type'] ?? 'EVENT');
        $activity->setTargetAudience($data['targetAudience'] ?? 'ALL');
        $activity->setIcon($data['icon'] ?? 'Calendar');
        $activity->setIsActive(true);

        $this->em->persist($activity);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $activity->getId()], Response::HTTP_CREATED);
    }
}
