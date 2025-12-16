<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function findByDate(string $date): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('DATE(i.issuedAt) = :date')
            ->setParameter('date', $date)
            ->orderBy('i.issuedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTime $from, \DateTime $to): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.issuedAt >= :from')
            ->andWhere('i.issuedAt <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('i.issuedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalsByMethod(string $date): array
    {
        // TODO: Add payment method to Invoice entity
        return [
            'efectivo' => 0,
            'tarjeta' => 0,
            'deposito' => 0,
        ];
    }
}
