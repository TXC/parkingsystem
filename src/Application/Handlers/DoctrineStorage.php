<?php

namespace App\Application\Handlers;

use App\Domain\Guard;
use App\Repository\GuardRepository;
use ArrayAccess;
use Countable;
use Doctrine\Common\Collections\Criteria;
use Iterator;

class DoctrineStorage implements ArrayAccess, Countable, Iterator
{
    private int $position = 0;

    public function __construct(
        private readonly GuardRepository $repository
    ) {
        $this->rewind();
    }

    public function offsetExists(mixed $offset): bool
    {
        $csrf = $this->repository->findOneBy(['name' => $offset]);
        return $csrf !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        $csrf = $this->repository->findOneBy(['name' => $offset]);
        if ($csrf === null) {
            return false;
        }
        return $csrf->getValue();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $csrf = $this->repository->findOneBy(['name' => $offset]);
        if ($csrf === null) {
            $csrf = new Guard();
        }
        $csrf->setName($offset);
        $csrf->setValue($value);
        $this->repository->add($csrf, true);
    }

    public function offsetUnset(mixed $offset): void
    {
        $csrf = $this->repository->findOneBy(['name' => $offset]);
        if ($csrf === null) {
            return;
        }
        $this->repository->remove($csrf, true);
    }

    public function count(): int
    {
        return $this->repository->count([]);
    }

    public function current(): mixed
    {
        $res = $this->repository->findOneBy(['id' => $this->position]);
        return $res->getValue();
    }

    public function next(): void
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->gt('id', $this->position))
                 ->setFirstResult(0)
                 ->setMaxResults(1)
                 ->orderBy(['id' => Criteria::ASC]);

        /** @var Guard|null $res */
        $res = $this->repository->matching($criteria);

        if (empty($res)) {
            $this->position = 0;
            return;
        }
        $this->position = $res->getId();
    }

    public function key(): mixed
    {
        $res = $this->repository->findOneBy(['id' => $this->position]);
        return $res->getName();
    }

    public function valid(): bool
    {
        $csrf = $this->repository->findOneBy(['id' => $this->position]);
        return $csrf !== null;
    }

    public function rewind(): void
    {
        $res = $this->repository->findOneBy([], ['id' => 'asc']);
        if (empty($res)) {
            $this->position = 0;
            return;
        }

        $this->position = $res->getId();
    }
}
