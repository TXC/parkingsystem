<?php

namespace App\Domain\Base;

use App\Domain\Operator;
use DateTimeImmutable;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait Timestamps
{
    #[
        ORM\Column(
            type: 'datetimetz_immutable',
            nullable: true,
            options: [
                'default' => '0000-00-00 00:00:00'
            ]
        ),
    ]
    protected ?DateTimeImmutable $createdAt = null;

    #[
        ORM\OneToOne(targetEntity: Operator::class),
        ORM\JoinColumn(nullable: true),
    ]
    protected ?Operator $createdBy = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    protected ?DateTimeImmutable $updatedAt = null;

    #[
        ORM\OneToOne(targetEntity: Operator::class),
        ORM\JoinColumn(nullable: true),
    ]
    protected ?Operator $updatedBy = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    protected ?DateTimeImmutable $deletedAt = null;

    #[
        ORM\OneToOne(targetEntity: Operator::class),
        ORM\JoinColumn(nullable: true),
    ]
    protected ?Operator $deletedBy = null;

    //region CreatedAt/CreatedBy
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $dateTimeImmutable): void
    {
        $this->createdAt = $dateTimeImmutable ?? new DateTimeImmutable();
    }

    public function getCreatedBy(): ?Operator
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Operator $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    #[ORM\PrePersist]
    public function setCreatedValues(ORMEvent\PrePersistEventArgs $eventArgs): void
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        $object->createdAt = new DateTimeImmutable();
    }
    //endregion CreatedAt/CreatedBy

    //region UpdatedAt/UpdatedBy
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $dateTimeImmutable): void
    {
        $this->updatedAt = $dateTimeImmutable ?? new DateTimeImmutable();
    }

    public function getUpdatedBy(): ?Operator
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Operator $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    #[ORM\PreUpdate]
    public function setUpdatedValues(ORMEvent\PreUpdateEventArgs $eventArgs): void
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        $object->updatedAt = new DateTimeImmutable();
    }
    //endregion UpdatedAt/UpdatedBy
}
