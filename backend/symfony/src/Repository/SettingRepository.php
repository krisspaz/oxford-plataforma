<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 *
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function getValue(string $name, $default = null)
    {
        $setting = $this->findOneBy(['name' => $name]);
        if (!$setting) return $default;

        $val = $setting->getValue();
        $type = $setting->getType();

        if ($type === 'boolean') return filter_var($val, FILTER_VALIDATE_BOOLEAN);
        if ($type === 'integer') return (int)$val;
        if ($type === 'json') return json_decode($val, true);

        return $val;
    }
}
