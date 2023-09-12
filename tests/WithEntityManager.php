<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use TXC\Box\Infrastructure\Environment\Settings;

trait WithEntityManager
{
    protected EntityManagerInterface $entityManager;

    protected function getEntityManager(): ?EntityManagerInterface
    {
        $settings = self::getContainer()->get(Settings::class);
        if ($settings->get('doctrine')) {
            return self::getContainer()->get(EntityManagerInterface::class);
        }
        return null;
    }

    protected function getRepository(string $repositoryName): ?EntityRepository
    {
        $settings = self::getContainer()->get(Settings::class);
        if ($settings->get('doctrine')) {
            return $this->getEntityManager()->getRepository($repositoryName);
        }
        return null;
    }
}
