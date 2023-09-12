<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Check;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class CheckRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Check');
    }

    public function findOneById(int $id): ?Check
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
