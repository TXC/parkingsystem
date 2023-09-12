<?php

declare(strict_types=1);

namespace App\Seeds;

use App\Domain\Check;
use App\Domain\Operator;
use App\Domain\Parking;
use App\Domain\Ticket;
use App\Domain\Token;
use App\Domain\Vehicle;
use App\Domain\Zone;
use App\Enums\InfractionEnum;
use App\Enums\PeriodEnum;
use App\Enums\TicketStatusEnum;
use TXC\Box\Testing\WithFaker;
use App\Tests\WithLicense;

/**
 * QUICK AND DIRTY....
 */

trait Seeder
{
    use WithFaker;
    use WithLicense;

    protected function createOperator(
        ?string $username = null,
        ?string $password = null
    ): Operator {
        $operator = new Operator();
        $operator->setUsername($username ?? self::getFaker()->username())
                 ->setPassword($password ?? self::getFaker()->password());

        $this->getEntityManager()->persist($operator);
        $this->getEntityManager()->flush();
        return $operator;
    }

    protected function getZone(?string $name = null, ?PeriodEnum $period = null): Zone
    {
        if ($name === null) {
            $zoneNames = ['A', 'B', 'C', 'D'];
            shuffle($zoneNames);
            $name = array_pop($zoneNames);
        }
        if ($period === null) {
            $values = PeriodEnum::cases();
            shuffle($values);
            $period = array_pop($values);
        }

        $zone = $this->getRepository(Zone::class)
                    ->findByNameAndPeriod($name, $period);
        return $zone;
    }

    protected function createVehicle(?string $licensePlate = null): Vehicle
    {
        $vehicle = new Vehicle();
        $vehicle->setLicensePlate($licensePlate ?? self::getLicensePlate());

        $this->getEntityManager()->persist($vehicle);
        $this->getEntityManager()->flush();
        return $vehicle;
    }

    protected function createCheck(
        ?Operator $operator = null,
        ?Zone $zone = null,
        ?Vehicle $vehicle = null,
        ?string $checkedAt = null,
    ): Check {
        $check = (new Check())
            ->setOperator($operator ?? $this->createOperator())
            ->setZone($zone ?? $this->createZone())
            ->setVehicle($vehicle ?? $this->createVehicle());

        if ($checkedAt === null) {
            $checkedAt = self::$faker->dateTimeBetween('-30 years', '+10 years')
                                     ->format('Y-m-d H:i:s');
            $check->setCheckedAt($checkedAt);
        }
        $this->getEntityManager()->persist($check);
        $this->getEntityManager()->flush();
        return $check;
    }

    protected function createParking(
        ?Zone $zone = null,
        ?Vehicle $vehicle = null,
        ?string $startedAt = null,
        ?string $expiresAt = null
    ): Parking {
        if ($zone === null) {
            $zone = $zone ?? $this->getZone();
        }
        $parking = new Parking();
        $parking->setZone($zone)
                ->setVehicle($vehicle ?? $this->createVehicle());

        if ($startedAt === null) {
            $startedAt = self::$faker->dateTimeBetween('-30 years', '+10 years')
                                     ->format('Y-m-d H:i:s');
        }
        $parking->setStartedAt($startedAt);

        if ($expiresAt !== null) {
            $parking->setExpiresAt($expiresAt);
        }

        $this->getEntityManager()->persist($parking);
        $this->getEntityManager()->flush();
        return $parking;
    }

    protected function createTicket(
        ?Zone $zone = null,
        ?Vehicle $vehicle = null,
        ?TicketStatusEnum $status = null,
        ?InfractionEnum $infraction = null,
        ?int $amount = null,
        ?string $issuedAt = null,
        ?string $dueAt = null,
    ): Ticket {
        $zone = $zone ?? $this->getZone();
        $vehicle = $vehicle ?? $this->createVehicle();

        if ($zone->getType() !== PeriodEnum::Day) {
            $zone = $this->getRepository(Zone::class)
                         ->findDayRateByName($zone->getName());
        }

        $ticket = new Ticket();
        if ($amount === null) {
            $ticket->setAmount($zone->getRate());
        }
        if ($issuedAt !== null) {
            $ticket->setIssuedAt($issuedAt);
        }
        if ($dueAt !== null) {
            $ticket->setDueAt($dueAt);
        }

        $ticket->setVehicle($vehicle)
               ->setZone($zone)
               ->setStatus($status ?? TicketStatusEnum::UnPaid)
               ->setInfraction($infraction ?? InfractionEnum::NoPayment);

        $this->getEntityManager()->persist($ticket);
        $this->getEntityManager()->flush();
        return $ticket;
    }

    protected function createToken(
        ?Operator $operator = null,
        ?Zone $zone = null
    ): Token {
        $operator = $operator ?? $this->createOperator();

        $token = new Token();
        $token->setOperator($operator)
              ->setZone($zone ?? $this->getZone(period: PeriodEnum::Day));

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
        return $token;
    }
}
