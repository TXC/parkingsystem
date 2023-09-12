<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Base\Common;
use App\Domain\Base\Timestamps;
use App\Enums\PeriodEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[
    ORM\Entity(repositoryClass: \App\Repository\ZoneRepository::class),
    ORM\UniqueConstraint(name: 'nameType', columns: ['name', 'type'])
]
class Zone extends Common implements DomainInterface
{
    #[ORM\Column(length: 10)]
    protected string $name;

    #[ORM\Column(options: ['unsigned' => true])]
    protected int $rate;

    #[ORM\Column(length: 6, enumType: PeriodEnum::class, options: ['default' => PeriodEnum::Hour])]
    protected PeriodEnum $type;

    /**
     * @return Collection<int, Parking>
     */
    #[ORM\OneToMany(targetEntity: Parking::class, mappedBy: 'zone')]
    protected Collection $parking;

    /**
     * @return Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'zone')]
    protected Collection $tickets;

    /**
     * @return Collection<int, Token>
     */
    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'zone')]
    protected Collection $tokens;

    /**
     * @return Collection<int, Check>
     */
    #[ORM\OneToMany(targetEntity: Check::class, mappedBy: 'zone')]
    protected Collection $checks;

    public function __construct()
    {
        $this->parking = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->checks = new ArrayCollection();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = parent::jsonSerialize();
        $payload['rate'] = (float) number_format($this->getRate() / 100, 2, '.', '');
        $payload['period'] = $payload['type'];
        unset(
            $payload['type'],
            $payload['parking'],
            $payload['ticket'],
            $payload['tokens']
        );

        return $payload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getType(): PeriodEnum
    {
        return $this->type;
    }

    public function setType(PeriodEnum $type): self
    {
        $this->type = $type;

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
     * @return Collection<int, Parking>
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
     * @return Collection<int, Token>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
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
