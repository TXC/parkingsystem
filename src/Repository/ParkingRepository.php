<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Check;
use App\Domain\Parking;
use App\Domain\Ticket;
use App\Domain\Token;
use App\Domain\Vehicle;
use App\Domain\Zone;
use App\Enums\InfractionEnum;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Types;

class ParkingRepository extends DomainRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, 'App\Domain\Parking');
    }

    /**
     * Check if vehicle have a valid parking session going
     */
    public function doVehicleHaveAValidParkingInZone(Vehicle $vehicle, Zone $zone): ?Parking
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.zone', 'z', 'WITH', 'z.name = :zone')
            ->andWhere(
                'p.vehicle = :vehicle',
                'p.expiresAt > :now'
            )
            ->setParameter('vehicle', $vehicle->getId(), Types::INTEGER)
            //->setParameter('zone', $zone->getId(), Types::INTEGER)
            ->setParameter('zone', $zone->getName(), Types::STRING)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneById(int $id): ?Parking
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * The main logic :D
     *
     */
    public function checkIfValid(Token $token, string $license): ?Ticket
    {
        // Find/Create Vehicle
        $vehicle = $this->getEntityManager()
                        ->getRepository(Vehicle::class)
                        ->findByLicensePlate($license);

        // Log the check for operator
        $checked = (new Check())
            ->setOperator($token->getOperator())
            ->setZone($token->getZone())
            ->setVehicle($vehicle);

        // Save the check to database, if we do it later it might not be saved
        $this->getEntityManager()->persist($checked);
        $this->getEntityManager()->flush();

        // Check if the vehicle has a valid parking session going
        $parked = $this->doVehicleHaveAValidParkingInZone($vehicle, $token->getZone());
        if (!empty($parked)) {
            // Valid session found, return null
            return null;
        }

        // Check if vehicle has a fresh ticket
        $parked = $this->getEntityManager()
                       ->getRepository(Ticket::class)
                       ->isTicketedVehicleAllowedToParkInZone($vehicle, $token->getZone());

        if (!empty($parked)) {
            // Ticket found, return null
            return null;
        }

        // Create a parking ticket for the vehicle
        $ticket = new Ticket();
        $ticket->setVehicle($vehicle)
               ->setZone($token->getZone())
               ->setAmount($token->getZone()->getRate())
               ->setInfraction(InfractionEnum::NoPayment);
        $vehicle->addTicket($ticket);

        $this->add($ticket, true);
        return $ticket;
    }
}
