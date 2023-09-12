<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Operator;
use App\Domain\Token;
use App\Domain\Zone;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class OperatorRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Operator');
    }

    /**
     * Find operator by username
     */
    public function findOneByUsername(string $username): ?Operator
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.username = :username')
            ->setParameter('username', $username, Types::STRING)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Find operator by id
     */
    public function findOneById(int $id): ?Operator
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Create token for operator on login
     */
    public function createTokenFor(Operator $operator, string $zone): Token
    {
        $zone = $this->getEntityManager()
                     ->getRepository(Zone::class)
                     ->findDayRateByName($zone);
        if (empty($zone)) {
            throw new \UnexpectedValueException('Invalid zone value');
        }
        $token = new Token();
        $token->setOperator($operator);
        $token->setZone($zone);
        $this->add($token, true);
        return $token;
    }
}
