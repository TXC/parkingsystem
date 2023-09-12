<?php

declare(strict_types=1);

namespace App\Domain;

use App\Enums\PeriodEnum;
use App\Domain\Base\Common;
use DateTimeImmutable;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[
    ORM\Entity(repositoryClass: \App\Repository\ParkingRepository::class),
    ORM\HasLifecycleCallbacks
]
class Parking extends Common implements DomainInterface
{
    #[
        ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'parking'),
        ORM\JoinColumn(nullable: false),
    ]
    protected Vehicle $vehicle;

    #[
        ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'parking'),
        ORM\JoinColumn(nullable: false),
    ]
    protected Zone $zone;

    #[ORM\Column(type: 'datetimetz_immutable')]
    protected ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: 'datetimetz_immutable')]
    protected ?DateTimeImmutable $expiresAt = null;

    #[ORM\Column(options: ['default' => 0])]
    protected int $cost = 0;

    public function __construct()
    {
        $this->startedAt = new DateTimeImmutable();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $cost = (float) number_format($this->getCost() / 100, 2, '.', '');

        return [
            'licenseplate' => $this->getVehicle()->getLicensePlate(),
            'zone' => $this->getZone()->getName(),
            'period' => $this->getZone()->getType(),
            'cost' => $cost,
            'startedat' => $this->getStartedAt()->format(self::DATETIME_FORMAT),
            'expiresat' => $this->getExpiresAt()->format(self::DATETIME_FORMAT),
            'id' => $this->getId(),
        ];
    }

    #[ORM\PrePersist]
    public function prePersistHook(ORMEvent\PrePersistEventArgs $eventArgs): self
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        if (empty($object->getZone())) {
            throw new \InvalidArgumentException('Missing required value: zone');
        }
        if (empty($object->getStartedAt())) {
            $object->startedAt = new DateTimeImmutable();
        }

        if (empty($object->getCost())) {
            $object->cost = $object->getZone()
                                   ->getRate();
        }
        if (empty($object->getExpiresAt())) {
            $object->expiresAt = $object->getZone()
                                        ->getType()
                                        ->toDateTime($object->getStartedAt());
        }

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

    public function getZone(): Zone
    {
        return $this->zone;
    }

    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->startedAt = $timestamp;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(string|PeriodEnum $period): self
    {
        if (is_string($period)) {
            $period = PeriodEnum::from($period);
        }
        $this->expiresAt = $period->toDateTime($this->getStartedAt());

        return $this;
    }
}
