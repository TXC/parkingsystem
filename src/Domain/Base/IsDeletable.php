<?php

namespace App\Domain\Base;

use App\Domain\Operator;
use DateTimeImmutable;
use Doctrine\ORM\Event as ORMEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait IsDeletable
{
    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    protected ?DateTimeImmutable $deletedAt = null;

    #[
        ORM\OneToOne(targetEntity: Operator::class),
        ORM\JoinColumn(nullable: true),
    ]
    protected ?Operator $deletedBy = null;

    //region DeletedAt/DeletedBy
    public function getDeletedAt(): DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $dateTimeImmutable): void
    {
        $this->deletedAt = $dateTimeImmutable ?? new DateTimeImmutable();
    }

    public function getDeletedBy(): ?Operator
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?Operator $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }

    #[ORM\PreRemove]
    public function setDeletedValues(ORMEvent\PreRemoveEventArgs $eventArgs): void
    {
        /** @var $object self */
        $object = $eventArgs->getObject();
        $object->deletedAt = new DateTimeImmutable();
    }
    //endregion DeletedAt/DeletedBy
}
