<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Base\Common;
use App\Domain\Base\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[ORM\Entity(repositoryClass: \App\Repository\VehicleRepository::class)]
class Vehicle extends Common implements DomainInterface
{
    #[ORM\Column(length: 10, unique: true)]
    protected string $licensePlate;

    /**
     * @return Collection<int, Parking>
     */
    #[ORM\OneToMany(targetEntity: Parking::class, mappedBy: 'vehicle')]
    protected Collection $parking;

    /**
     * @return Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'vehicle')]
    protected Collection $tickets;

    /**
     * @return Collection<int, Check>
     */
    #[ORM\OneToMany(targetEntity: Check::class, mappedBy: 'vehicle')]
    protected Collection $checks;

    public function __construct()
    {
        $this->parking = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->checks = new ArrayCollection();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = parent::jsonSerialize();
        unset(
            $payload['parking'],
            $payload['ticket'],
        );

        return $payload;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $licensePlate = strtoupper($licensePlate);
        $licensePlate = preg_replace('/[^A-Z0-9]/', '', $licensePlate);
        $this->licensePlate = $licensePlate;

        return $this;
    }

    /**
     * @return Collection<int, Parking>
     */
    public function getParking(): Collection
    {
        return $this->parking;
    }

    public function addParking(Parking $parking): self
    {
        if (!$this->parking->contains($parking)) {
            $this->parking->add($parking);
        }

        return $this;
    }

    public function removeParking(Parking $parking): self
    {
        if ($this->parking->contains($parking)) {
            $this->parking->removeElement($parking);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicket(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
        }

        return $this;
    }

    /**
     * @return Collection<int, Check>
     */
    public function getChecks(): Collection
    {
        return $this->checks;
    }

    public function addCheck(Check $check): self
    {
        if (!$this->checks->contains($check)) {
            $this->checks->add($check);
        }

        return $this;
    }

    public function removeCheck(Check $check): self
    {
        if ($this->checks->contains($check)) {
            $this->checks->removeElement($check);
        }

        return $this;
    }
}
