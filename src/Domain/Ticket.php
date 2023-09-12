<?php

declare(strict_types=1);

namespace App\Domain;

use App\Enums\TicketStatusEnum;
use App\Domain\Zone;
use App\Domain\Vehicle;
use App\Domain\Base\Common;
use App\Enums\InfractionEnum;
use App\Enums\PeriodEnum;
use DateTimeImmutable;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Infrastructure\Environment\Settings;
use TXC\Box\Interfaces\DomainInterface;

#[
    ORM\Entity(repositoryClass: \App\Repository\TicketRepository::class),
    ORM\HasLifecycleCallbacks
]
class Ticket extends Common implements DomainInterface
{
    //use Timestamps;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'tickets')]
    protected Zone $zone;

    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'tickets')]
    protected Vehicle $vehicle;

    #[ORM\Column]
    protected int $amount = 0;

    #[ORM\Column(type: 'datetimetz_immutable')]
    protected ?DateTimeImmutable $issuedAt = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    protected ?DateTimeImmutable $dueAt = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    protected ?DateTimeImmutable $paidAt = null;

    #[ORM\Column(length: 10, enumType: InfractionEnum::class, nullable: true)]
    protected ?InfractionEnum $infraction = null;

    #[ORM\Column(length: 6, enumType: TicketStatusEnum::class, options: ['default' => TicketStatusEnum::UnPaid])]
    protected TicketStatusEnum $status = TicketStatusEnum::UnPaid;

    public function __construct()
    {
        $this->issuedAt = new DateTimeImmutable();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = parent::jsonSerialize();
        $payload['amount'] = (float) number_format($this->getAmount() / 100, 2, '.', '');

        return $payload;
    }

    #[ORM\PrePersist]
    public function prePersistHook(ORMEvent\PrePersistEventArgs $eventArgs): self
    {
        /** @var $object self */
        $object = $eventArgs->getObject();

        $amount = $eventArgs->getObjectManager()
                            ->getRepository(Zone::class)
                            ->findDayRateByName($object->getZone()->getName());
        $object->amount = $amount->getRate();
        //if (empty($object->amount)) {
        //    $object->amount = $object->zone->getRate();
        //}

        if (empty($object->issuedAt)) {
            $object->issuedAt = new DateTimeImmutable();
        }

        if (empty($object->dueAt)) {
            $dueTime = Settings::load()->get('application.ticket.due');
            $interval = \DateInterval::createFromDateString($dueTime);

            $object->dueAt = $object->getIssuedAt()?->add($interval);
        }

        if (
            $object->status === TicketStatusEnum::Paid
            && empty($object->paidAt)
        ) {
            $object->paidAt = new DateTimeImmutable();
        }

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdateHook(ORMEvent\PreUpdateEventArgs $eventArgs): self
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        if (
            $object->status === TicketStatusEnum::Paid
            && empty($object->paidAt)
        ) {
            $object->paidAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getZone(): Zone
    {
        return $this->zone;
    }

    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getIssuedAt(): DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->issuedAt = $timestamp;

        return $this;
    }

    public function getDueAt(): ?DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function setDueAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->dueAt = $timestamp;

        return $this;
    }

    public function getPaidAt(): ?DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->paidAt = $timestamp;

        return $this;
    }

    public function getStatus(): TicketStatusEnum
    {
        return $this->status;
    }

    public function setStatus(string|TicketStatusEnum $status): self
    {
        if (is_string($status)) {
            $status = TicketStatusEnum::from($status);
        }
        $this->status = $status;

        return $this;
    }

    public function getInfraction(): InfractionEnum
    {
        return $this->infraction;
    }

    public function setInfraction(string|InfractionEnum $infraction): self
    {
        if (is_string($infraction)) {
            $infraction = InfractionEnum::from($infraction);
        }
        $this->infraction = $infraction;

        return $this;
    }
}
