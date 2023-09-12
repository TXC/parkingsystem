<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Actions\Action;
use App\Domain\Parking;
use App\Domain\Token;
use App\Domain\Vehicle;
use App\Domain\Zone;
use App\Enums\PeriodEnum;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use TXC\Box\Actions\ActionPayload;
use TXC\Box\Interfaces\RestInterface;

//class ParkingController extends Action implements RestInterface
class ParkingController extends Action
{
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }

    public function store(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->setRequestResponse($request, $response);

        $zone = $this->request->getAttribute('zone');

        $params = $this->getFormData();

        if (empty($params['license'])) {
            throw new HttpBadRequestException($this->request, 'Missing license parameter');
        }
        if (empty($params['period'])) {
            $params['period'] = PeriodEnum::Hour->value;
        }

        $zoneObject = $this->getRepository(Zone::class)
                           ->findByNameAndPeriod($zone, PeriodEnum::from($params['period']));
        if (empty($zoneObject)) {
            throw new HttpNotFoundException($this->request, 'Invalid zone: ' . $zone);
        }

        // Find existing, or create a new vehicle, then connect it
        $vehicle = $this->getRepository(Vehicle::class)->findOneByLicensePlate($params['license']);
        if (empty($vehicle)) {
            $vehicle = new Vehicle();
            $vehicle->setLicensePlate($params['license']);
            $this->getEntityManager()->persist($vehicle);
        }

        $object = new Parking();
        $object->setExpiresAt($params['period'])
               ->setZone($zoneObject)
               ->setVehicle($vehicle);

        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
        return $this->respondWithData($object);
    }

    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }

    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }
    public function destroy(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }
}
