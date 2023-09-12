<?php

declare(strict_types=1);

namespace App\Tests\unit\Vehicle;

use App\Seeds\Seeder;
use App\Tests\WithLicense;
use PHPUnit\Framework\Attributes\DataProvider;

class VehicleTest extends \App\Tests\ContainerTestCase
{
    use WithLicense;
    use Seeder;

    #[DataProvider('licensePlateProvider')]
    public function testGetters(string $licensePlate, string $expected): void
    {
        $vehicle = $this->createVehicle($licensePlate);

        $this->assertEquals($expected, $vehicle->getLicensePlate());
    }

    public static function licensePlateProvider(): array
    {
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $licensePlate = self::getLicensePlate();
            $expected = preg_replace('/[^A-Z0-9]/', '', $licensePlate);

            $data[] = [$licensePlate, $expected];
        }
        return $data;
    }
}
