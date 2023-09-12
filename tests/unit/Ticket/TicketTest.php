<?php

declare(strict_types=1);

namespace App\Tests\unit\Ticket;

use App\Domain\Zone;
use App\Enums\TicketStatusEnum;
use App\Seeds\Seeder;
use App\Tests\WithLicense;
use TXC\Box\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;

class TicketTest extends \App\Tests\ContainerTestCase
{
    use WithFaker;
    use WithLicense;
    use Seeder;

    #[DataProvider('ticketProvider')]
    public function testTicketUnPaid(string $license): void
    {
        $zone = $this->getZone();
        $vehicle = $this->createVehicle($license);

        $ticket = $this->createTicket(
            zone: $zone,
            vehicle: $vehicle,
            status: TicketStatusEnum::UnPaid
        );

        $ticketZone = $this->getRepository(Zone::class)
                           ->findDayRateByName($zone->getName());

        $this->assertEquals(TicketStatusEnum::UnPaid, $ticket->getStatus());
        $this->assertEquals($ticketZone->getRate(), $ticket->getAmount());
        $this->assertNotNull($ticket->getIssuedAt(), 'Failed asserting that \'issuedAt\' is not null');
        $this->assertNotNull($ticket->getDueAt(), 'Failed asserting that \'dueAt\' is not null');
        $this->assertEquals(
            $vehicle->getLicensePlate(),
            $ticket->getVehicle()->getLicensePlate()
        );
    }

    #[DataProvider('ticketProvider')]
    public function testTicketPaid(string $license): void
    {
        $zone = $this->getZone();
        $vehicle = $this->createVehicle($license);
        $ticket = $this->createTicket(
            zone: $zone,
            vehicle: $vehicle,
            status: TicketStatusEnum::UnPaid
        );

        $ticketZone = $this->getRepository(Zone::class)
                           ->findDayRateByName($zone->getName());

        $this->assertEquals(TicketStatusEnum::UnPaid, $ticket->getStatus());
        $this->assertEquals($ticketZone->getRate(), $ticket->getAmount());
        $this->assertNotNull($ticket->getIssuedAt(), 'Failed asserting that \'issuedAt\' is not null');
        $this->assertNotNull($ticket->getDueAt(), 'Failed asserting that \'dueAt\' is not null');
        $this->assertEquals(
            $vehicle->getLicensePlate(),
            $ticket->getVehicle()->getLicensePlate()
        );

        $ticket->setStatus(TicketStatusEnum::Paid);
        $this->getEntityManager()->flush();

        $this->assertEquals(TicketStatusEnum::Paid, $ticket->getStatus());
        $this->assertNotNull($ticket->getPaidAt(), 'Failed asserting that \'paidAt\' is not null');
    }

    public static function ticketProvider(): array
    {
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                self::getLicensePlate(),
            ];
        }
        return $data;
    }
}
