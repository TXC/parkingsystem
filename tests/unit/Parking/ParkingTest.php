<?php

declare(strict_types=1);

namespace App\Tests\unit\Parking;

use App\Enums\PeriodEnum;
use App\Seeds\Seeder;
use App\Tests\WithLicense;
use TXC\Box\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;

class ParkingTest extends \App\Tests\ContainerTestCase
{
    use WithFaker;
    use WithLicense;
    use Seeder;

    #[DataProvider('parkingProvider')]
    public function testGetters(string $license): void
    {
        $faker = self::getFaker();

        $startTime = $faker->dateTimeBetween('now', '+10 years');
        $started = $startTime->format('c');

        $vehicle = $this->createVehicle($license);
        $parking = $this->createParking(vehicle: $vehicle);
        $periodType = $parking->getZone()->getType();
        $expires = $periodType->toDateTime($startTime);

        $parking->setStartedAt($started)
                ->setExpiresAt($periodType);

        $this->assertEquals($started, $parking->getStartedAt()->format('c'));
        $this->assertEquals($expires->format('c'), $parking->getExpiresAt()->format('c'));
    }

    public static function parkingProvider(): array
    {
        $faker = self::getFaker();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                self::getLicensePlate(),
            ];
        }
        return $data;
    }
}
