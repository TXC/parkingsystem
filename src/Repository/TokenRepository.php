<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Token;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class TokenRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Token');
    }

    /**
     * Find entity by token
     */
    public function findOneByToken(string $token): ?Token
    {
        return $this->createQueryBuilder('p')
            ->andWhere(
                'p.token = :token',
                'p.expiresAt > :now'
            )
            ->orderBy('p.expiresAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('token', $token, Types::STRING)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneById(int $id): ?Token
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
