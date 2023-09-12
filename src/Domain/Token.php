<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Zone;
use App\Domain\Base\Common;
use DateTimeImmutable;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[
    ORM\Entity(repositoryClass: \App\Repository\TokenRepository::class),
    ORM\HasLifecycleCallbacks
]
class Token extends Common implements DomainInterface
{
    #[ORM\ManyToOne(targetEntity: Operator::class, inversedBy: 'tokens')]
    protected Operator $operator;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'tokens')]
    protected Zone $zone;

    #[ORM\Column(type: 'text', length: 32)]
    protected string $token;

    #[ORM\Column(type: 'datetimetz_immutable')]
    protected DateTimeImmutable $expiresAt;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'expiresAt' => $this->getExpiresAt()->format(self::DATETIME_FORMAT),
            'token' => $this->getToken(),
        ];
    }

    #[ORM\PrePersist]
    public function prePersistHook(ORMEvent\PrePersistEventArgs $eventArgs): void
    {
        /** @var $object self */
        $object = $eventArgs->getObject();

        $bytes = random_bytes(12);
        $object->token = bin2hex($bytes);
        $object->expiresAt = (new DateTimeImmutable())->modify('+8 hours');
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

    public function getZone(): Zone
    {
        return $this->zone;
    }

    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(string|DateTimeImmutable|null $timestamp = null): self
    {
        if (is_string($timestamp)) {
            $timestamp = new DateTimeImmutable($timestamp);
        }
        $this->expiresAt = $timestamp;

        return $this;
    }
}
