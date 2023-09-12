<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Actions\Action;
use App\Domain\Vehicle;
use App\Domain\Parking;
use App\Domain\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use TXC\Box\Actions\ActionPayload;
use TXC\Box\Interfaces\RestInterface;

//class OperatorController extends Action implements RestInterface
class OperatorController extends Action
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
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }

    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->setRequestResponse($request, $response);

        $apiKey = $this->request->getHeaderLine('x-api-key');
        /** @var $token Token|null */
        $token = $this->getRepository(Token::class)->findOneByToken($apiKey);
        if (empty($token)) {
            throw new HttpForbiddenException($this->request, 'Missing valid token');
        }

        $license = $this->request->getAttribute('license');

        if (empty($license)) {
            throw new HttpBadRequestException($this->request, 'Missing license parameter');
        }

        $object = $this->getRepository(Parking::class)
                       ->checkIfValid(
                           $token,
                           $license
                       );
        if (empty($object)) {
            return $this->respond(new ActionPayload(200, 'OK'));
        }
        return $this->respondWithData($object);
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
