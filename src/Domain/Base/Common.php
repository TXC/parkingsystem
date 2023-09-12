<?php

declare(strict_types=1);

namespace App\Domain\Base;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Common extends Entity
{
    #[
        ORM\Id,
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue(strategy: 'AUTO'),
    ]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
