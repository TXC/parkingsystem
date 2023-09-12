<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use TXC\Box\Infrastructure\DependencyInjection\ContainerFactory;

return ContainerFactory::createForTestSuite()->get(EntityManagerInterface::class);
