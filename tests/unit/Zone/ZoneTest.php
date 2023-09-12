<?php

declare(strict_types=1);

namespace App\Tests\unit\Driver;

use App\Domain\Zone;
use App\Enums\PeriodEnum;
use TXC\Box\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;

class ZoneTest extends \App\Tests\ContainerTestCase
{
    use WithFaker;

    #[DataProvider('zoneProvider')]
    public function testGetters(string $name, int $rate, string $period): void
    {
        $zone = new Zone();
        $zone->setName($name)
             ->setRate($rate)
             ->setType(PeriodEnum::from($period));

        $this->assertEquals($name, $zone->getName());
        //$this->assertIsInt($zone->getId());
    }

    public static function zoneProvider(): array
    {
        $faker = self::getFaker();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                $faker->asciify(),
                $faker->randomNumber(),
                $faker->randomElement(['hour', 'day']),
            ];
        }
        return $data;
    }
}
