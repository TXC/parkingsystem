<?php

declare(strict_types=1);

namespace App\Tests\integration\Operator;

use App\Domain\Token;
use App\Domain\Zone;
use App\Enums\PeriodEnum;
use App\Seeds\Seeder;
use App\Tests\WithLicense;
use TXC\Box\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;

class OperatorTest extends \App\Tests\WebTestCase
{
    use WithFaker;
    use WithLicense;
    use Seeder;


    #[DataProvider('operatorProvider')]
    public function testOperatorCanLogin(string $username, string $password, string $zone): void
    {
        $this->createOperator($username, $password);
        $request = $this->createRequest('POST', '/login')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => $username,
                'password' => $password,
                'zone' => $zone,
            ]);

            $response = $this->getApplication()->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
        $this->assertIsString($payload['data']['expiresAt']);
        $this->assertIsString($payload['data']['token']);
        $this->assertEquals(24, strlen($payload['data']['token']));
        $this->assertTrue(new \DateTime() < new \DateTime($payload['data']['expiresAt']));
    }

    #[DataProvider('operatorProvider')]
    public function testOperatorCanLogout(string $username, string $password, string $zone): void
    {
        $this->createOperator($username, $password);
        $request = $this->createRequest('POST', '/login')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => $username,
                'password' => $password,
                'zone' => $zone,
            ]);

        $response = $this->getApplication()->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $tokenRepository = $this->getRepository(Token::class);
        $token = $tokenRepository->findOneByToken($payload['data']['token']);
        $this->assertIsObject($token);

        $request = $this->createRequest('GET', '/logout')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token->getToken())
            ->withParsedBody([
                'username' => $username,
                'password' => $password,
                'zone' => $zone,
            ]);

        $response = $this->getApplication()
                         ->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsString($payload['data']);

        $tokenCheck = $tokenRepository->findOneByToken($token->getToken());
        $this->assertNull($tokenCheck);
    }

    public function testOperatorChecksVehicleAndItsAllowedToPark(): void
    {
        $zone = $this->getZone(period: PeriodEnum::Hour);
        $tokenObject = $this->createToken(zone: $zone);
        $parking = $this->createParking(zone: $zone, startedAt: '-5 minutes');

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $response = $this->getApplication()
                         ->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsString($payload['data']);
        $this->assertEquals('OK', $payload['data']);
    }

    public function testOperatorChecksVehicleThatIsOverDue(): void
    {
        $zone = $this->getZone(period: PeriodEnum::Hour);
        $tokenObject = $this->createToken(zone: $zone);
        $parking = $this->createParking(zone: $zone, startedAt: '-3 hour');

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
    }

    public function testOperatorChecksVehicleThatHasNotPayedAndGetsATicket(): void
    {
        $zone = $this->getZone(period: PeriodEnum::Hour);
        $tokenObject = $this->createToken(zone: $zone);

        $license = $this->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
    }

    public function testOperatorChecksNonTicketedVehicleThatHasMovedToALowerRatedZoneAndGetsATicket(): void
    {
        $zoneRepository = $this->getRepository(Zone::class);

        $zone = $zoneRepository->getZoneThatIsExpensive(PeriodEnum::Hour);
        $parking = $this->createParking(zone: $zone, startedAt: '-10 minutes');

        $lowerRatedZone = $zoneRepository->getLowerRatedZone($zone);
        $tokenObject = $this->createToken(zone: $lowerRatedZone);

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
    }


    public function testOperatorChecksNonTicketedVehicleThatHasMovedToAHigherRatedZoneAndGetsATicket(): void
    {
        $zoneRepository = $this->getRepository(Zone::class);

        $zone = $zoneRepository->getZoneThatIsCheap(PeriodEnum::Hour);
        $parking = $this->createParking(zone: $zone, startedAt: '-10 minutes');

        $higherRatedZone = $zoneRepository->getHigherRatedZone($zone);
        $tokenObject = $this->createToken(zone: $higherRatedZone);

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
    }


    public function testOperatorChecksVehicleThatHasBeenTicketedAndThenMovedToLowerZoneAndDoesntGetATicket(): void
    {
        $zoneRepository = $this->getRepository(Zone::class);

        $zone = $zoneRepository->getZoneThatIsExpensive(PeriodEnum::Hour);
        $parking = $this->createParking(zone: $zone, startedAt: '-3 hour');
        $ticket = $this->createTicket(
            zone: $parking->getZone(),
            vehicle: $parking->getVehicle()
        );

        $lowerRatedZone = $zoneRepository->getLowerRatedZone($ticket->getZone());
        $tokenObject = $this->createToken(zone: $lowerRatedZone);

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsString($payload['data']);
        $this->assertEquals('OK', $payload['data']);
    }

    public function testOperatorChecksVehicleThatHasBeenTicketedAndThenMovedToHigherZoneAndGetsATicket(): void
    {
        $zoneRepository = $this->getRepository(Zone::class);

        $zone = $zoneRepository->getZoneThatIsCheap(PeriodEnum::Hour);
        $parking = $this->createParking(zone: $zone, startedAt: '-3 hour');
        $ticket = $this->createTicket(
            zone: $parking->getZone(),
            vehicle: $parking->getVehicle()
        );

        $higherRatedZone = $zoneRepository->getHigherRatedZone($ticket->getZone());
        $tokenObject = $this->createToken(zone: $higherRatedZone);

        $license = $parking->getVehicle()->getLicensePlate();
        $token = $tokenObject->getToken();

        $request = $this->createRequest('GET', '/parking/validate/' . $license)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-KEY', $token);

        $app = $this->getApplication();
        $response = $app->handle($request);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $payload['statusCode']);
        $this->assertIsArray($payload['data']);
    }

    public static function operatorProvider(): array
    {
        $faker = self::getFaker();
        $data = [];
        for ($i = 0; $i < 1; $i++) {
            $data[] = [
                $faker->userName(),
                $faker->password(),
                $faker->randomElement(['A', 'B', 'C', 'D']),
            ];
        }
        return $data;
    }
}
