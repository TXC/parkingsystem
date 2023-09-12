<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Actions\Action;
use App\Domain\Parking;
use App\Enums\TicketStatusEnum;
use App\Domain\Ticket;
use App\Domain\Vehicle;
use App\Domain\Zone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use TXC\Box\Actions\ActionPayload;
use TXC\Box\Interfaces\RestInterface;

//class TicketController extends Action implements RestInterface
class TicketController extends Action
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
        $id = $this->request->getAttribute('ticketId');

        if (empty($id)) {
            throw new HttpNotFoundException($this->request, 'Missing zone parameter');
        }
        $ticket = $this->getRepository(Ticket::class)->findOneById($id);
        if (empty($zone)) {
            throw new HttpNotFoundException($this->request, 'Invalid ticket: ' . $id);
        }

        return $this->respondWithData($ticket);
    }

    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->setRequestResponse($request, $response);

        $ticketId = $this->request->getAttribute('ticketId');

        $ticket = $this->getRepository(Ticket::class)->findOneById($ticketId);
        if (empty($ticket)) {
            throw new HttpNotFoundException($this->request, 'Invalid ticket: ' . $ticketId);
        }

        $params = $this->getFormData();

        if (empty($params['amount'])) {
            throw new HttpBadRequestException($this->request, 'Missing amount parameter');
        }
        if ($params['amount'] != $ticket->getAmount()) {
            throw new HttpBadRequestException($this->request, 'Invalid amount parameter');
        }

        $ticket->setStatus(TicketStatusEnum::Paid);

        $this->getEntityManager()->persist($ticket);
        $this->getEntityManager()->flush();
        return $this->respondWithData($ticket);
    }

    public function destroy(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respond(new ActionPayload(405));
    }
}
