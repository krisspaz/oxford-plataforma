<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findOverduePayments(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->andWhere('p.dueDate < :now')
            ->setParameter('status', 'PENDING')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalsByDate(\DateTimeInterface $date): array
    {
        $start = clone $date;
        $start->setTime(0, 0, 0);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->select('p.method, SUM(p.amount) as total')
            ->where('p.paymentDate BETWEEN :start AND :end')
            ->andWhere('p.status = :status')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', 'PAID')
            ->groupBy('p.method')
            ->getQuery()
            ->getResult();
    }
}
