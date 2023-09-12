<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Ticket;
use App\Domain\Vehicle;
use App\Domain\Zone;
use App\Enums\PeriodEnum;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class TicketRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Ticket');
    }


    /**
     * Check if vehicle has a fresh ticket
     */
    public function isTicketedVehicleAllowedToParkInZone(
        Vehicle $vehicle,
        Zone $zone
    ): ?Ticket {
        $startDate = new \DateTimeImmutable('midnight');
        $endDate = new \DateTimeImmutable('tomorrow midnight');

        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->andWhere(
                't.vehicle = :vehicle',
                't.amount >= :currentZoneRate',
                $qb->expr()->between('t.issuedAt', ':start', ':stop')
            )
            ->setParameter('currentZoneRate', $zone->getRate(), Types::INTEGER)
            ->setParameter('vehicle', $vehicle->getId(), Types::INTEGER)
            ->setParameter('start', $startDate, Types::DATETIME_IMMUTABLE)
            ->setParameter('stop', $endDate, Types::DATETIME_IMMUTABLE)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findOneById(int $id): ?Ticket
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
