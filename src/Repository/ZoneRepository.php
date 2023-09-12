<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Zone;
use App\Enums\PeriodEnum;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class ZoneRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Zone');
    }

    /**
     * Get zone entity by name and period
     */
    public function findByNameAndPeriod(string $name, string|PeriodEnum $period): ?Zone
    {
        if (is_string($period)) {
            $period = PeriodEnum::from($period);
        }
        return $this->createQueryBuilder('z')
            ->andWhere('z.name = :name')
            ->andWhere('z.type = :type')
            ->setParameter('name', $name, Types::STRING)
            //->setParameter('type', $period, Types::STRING)
            ->setParameter('type', $period)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Get day rate of zone by name
     */
    public function findDayRateByName(string $name): ?Zone
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.name = :name')
            ->andWhere('z.type = :type')
            ->setParameter('name', $name, Types::STRING)
            //->setParameter('type', PeriodEnum::Day, Types::STRING)
            ->setParameter('type', PeriodEnum::Day)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Fetch lower rated rows, shuffle them around and return one
     * Since Doctrine doesn't support randomization
     */
    public function getLowerRatedZone(Zone $zone): ?Zone
    {
        $results = $this->createQueryBuilder('z')
            ->andWhere(
                'z.rate < :rate',
                'z.type = :type',
            )
            ->orderBy('z.rate', 'ASC')
            ->setParameter('rate', $zone->getRate(), Types::INTEGER)
            //->setParameter('type', $zone->getType(), Types::STRING)
            ->setParameter('type', $zone->getType())
            ->getQuery()
            ->getResult();

        shuffle($results);
        return array_pop($results);
    }

    /**
     * Fetch higher rated rows, shuffle them around and return one
     * Since Doctrine doesn't support randomization
     */
    public function getHigherRatedZone(Zone $zone): ?Zone
    {
        $results = $this->createQueryBuilder('z')
            ->andWhere(
                'z.rate > :rate',
                'z.type = :type',
            )
            ->orderBy('z.rate', 'ASC')
            ->setParameter('rate', $zone->getRate(), Types::INTEGER)
            //->setParameter('type', $zone->getType(), Types::STRING)
            ->setParameter('type', $zone->getType())
            ->getQuery()
            ->getResult();

        shuffle($results);
        return array_pop($results);
    }

    /**
     * Fetch 3 rows, shuffle them around, and return one
     * Since Doctrine doesn't support randomization
     */
    public function getZoneThatIsCheap(PeriodEnum $period): Zone
    {
        $results = $this->createQueryBuilder('z')
            ->andWhere(
                'z.type = :type',
            )
            ->orderBy('z.rate', 'ASC')
            ->setMaxResults(3)
            //->setParameter('type', $zone->getType(), Types::STRING)
            ->setParameter('type', $period)
            ->getQuery()
            ->getResult();

        shuffle($results);
        return array_pop($results);
    }

    /**
     * Fetch 3 rows, shuffle them around, and return one
     * Since Doctrine doesn't support randomization
     */
    public function getZoneThatIsExpensive(PeriodEnum $period): Zone
    {
        $results = $this->createQueryBuilder('z')
            ->andWhere(
                'z.type = :type',
            )
            ->orderBy('z.rate', 'DESC')
            ->setMaxResults(3)
            //->setParameter('type', $zone->getType(), Types::STRING)
            ->setParameter('type', $period)
            ->getQuery()
            ->getResult();

        shuffle($results);
        return array_pop($results);
    }

    public function findOneById(int $id): ?Zone
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
