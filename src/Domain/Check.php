<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Base\Common;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[
    ORM\Entity(repositoryClass: \App\Repository\CheckRepository::class),
    ORM\Table(name: '`check`')
]
class Check extends Common implements DomainInterface
{
    #[ORM\ManyToOne(targetEntity: Operator::class, inversedBy: 'checks')]
    protected Operator $operator;

    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'checks')]
    protected Vehicle $vehicle;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'checks')]
    protected Zone $zone;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    protected ?DateTimeImmutable $checkedAt = null;

    public function __construct()
    {
        $this->checkedAt = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function prePersistHook(ORMEvent\PrePersistEventArgs $eventArgs): self
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        if (empty($object->getCheckedAt())) {
            $object->checkedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getCheckedAt(): DateTimeImmutable
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->checkedAt = $timestamp;

        return $this;
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): self
    {
        $this->operator = $operator;

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

}
