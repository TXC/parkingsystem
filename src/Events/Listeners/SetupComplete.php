<?php

declare(strict_types=1);

namespace App\Events\Listeners;

use App\Domain\Zone;
use App\Enums\PeriodEnum;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use TXC\Box\Infrastructure\Environment\Settings;

class SetupComplete implements \League\Event\Listener
{
    private EntityManagerInterface $em;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
        $this->em = $this->container->get(EntityManagerInterface::class);
    }

    public function __invoke(object $event): void
    {
        $this->insertPayload($this->zoneData());
    }

    private function zoneData(): array
    {
        $data = [];
        $settings = $this->container->get(Settings::class);
        foreach ($settings->get('application.rate') as $name => $rates) {
            foreach ($rates as $period => $rate) {
                $periodType = PeriodEnum::from($period);
                $data[] = (new Zone())
                        ->setName($name)
                        ->setType($periodType)
                        ->setRate(intval(round($rate * 100)));
            }
        }
        return $data;
    }

    private function insertPayload(array $payload): void
    {
        foreach ($payload as $item) {
            $this->em->persist($item);
        }
        $this->em->flush();
    }
}
