<?php

declare(strict_types=1);

namespace App\Tests\integration\Driver;

use App\Tests\WithLicense;
use TXC\Box\Testing\WithFaker;
use PHPUnit\Framework\Attributes\TestWith;

class DriverTest extends \App\Tests\WebTestCase
{
    use WithFaker;
    use WithLicense;

    #[TestWith(['A'])]
    #[TestWith(['B'])]
    #[TestWith(['C'])]
    #[TestWith(['D'])]
    public function testDriverParksInZoneAndPaysForOneHour(string $zone): void
    {
        $request = $this->createRequest(
            'POST',
            '/parking/' . $zone,
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        );
        $licensePlate = self::getLicensePlate();
        $expected = preg_replace('/[^A-Z0-9]/', '', $licensePlate);

        $request = $request->withParsedBody([
            'license' => $licensePlate,
            'period' => 'hour'
        ]);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $actualStartedAt = new \DateTime($payload['data']['startedat']);
        $actualExpiresAt = new \DateTime($payload['data']['expiresat']);
        $diff = $actualExpiresAt->diff($actualStartedAt);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
        $this->assertEquals($zone, $payload['data']['zone']);
        $this->assertEquals($expected, $payload['data']['licenseplate']);
        $this->assertEquals(1, $diff->format('%h'));
    }

    #[TestWith(['A'])]
    #[TestWith(['B'])]
    #[TestWith(['C'])]
    #[TestWith(['D'])]
    public function testDriverParksInZoneAndPaysForOneDay(string $zone): void
    {
        $request = $this->createRequest(
            'POST',
            '/parking/' . $zone,
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        );
        $licensePlate = self::getLicensePlate();
        $expected = preg_replace('/[^A-Z0-9]/', '', $licensePlate);

        $request = $request->withParsedBody([
            'license' => $licensePlate,
            'period' => 'day'
        ]);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        //$actualStartedAt = new \DateTime($payload['data']['startedAt']);
        $actualExpiresAt = new \DateTime($payload['data']['expiresat']);
        $expectedExpiresAt = new \DateTime('tomorrow');

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
        $this->assertEquals($zone, $payload['data']['zone']);
        $this->assertEquals($expected, $payload['data']['licenseplate']);
        $this->assertEquals($expectedExpiresAt->format('c'), $actualExpiresAt->format('c'));
    }
}
