<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Vehicle;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class VehicleRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Vehicle');
    }

    /**
     * Find vehicle by license plate
     */
    public function findByLicensePlate(string $licensePlate): Vehicle
    {
        $result = $this->createQueryBuilder('p')
            ->andWhere('p.licensePlate = :licensePlate')
            ->setParameter('licensePlate', $licensePlate)
            ->getQuery()
            ->getOneOrNullResult()
            ;
        if ($result === null) {
            $result = new Vehicle();
            $result->setLicensePlate($licensePlate);
            $this->add($result, true);
        }
        return $result;
    }

    public function findOneById(int $id): ?Vehicle
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
