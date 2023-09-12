<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Actions\Action;
use App\Domain\Operator;
use App\Domain\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use TXC\Box\Actions\ActionPayload;

// Simple login handler

class LoginController extends Action
{
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->setRequestResponse($request, $response);
        $apiKey = $this->request->getHeaderLine('x-api-key');

        $repository = $this->getRepository(Token::class);
        $token = $repository->findOneByToken($apiKey);
        if (empty($token)) {
            throw new HttpForbiddenException($this->request, 'Nope');
        }
        $repository->remove($token, true);
        $_SESSION['user'] = null;
        return $this->respond(new ActionPayload(200, 'OK'));
    }

    public function store(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $this->setRequestResponse($request, $response);
        $params = $request->getParsedBody();

        if (empty($params['username'])) {
            throw new HttpBadRequestException($this->request, 'Missing username parameter');
        }
        if (empty($params['password'])) {
            throw new HttpBadRequestException($this->request, 'Missing password parameter');
        }
        if (empty($params['zone'])) {
            throw new HttpBadRequestException($this->request, 'Missing zone parameter');
        }

        //find user by login
        $operatorRepository = $this->getRepository(Operator::class);
        $operator = $operatorRepository->findOneByUsername($params['username']);

        //verify if hash from database matches hash of provided password
        if (
            !empty($operator)
            && password_verify($params['password'], $operator->getPassword())
        ) {
            $_SESSION['user'] = $operator;
            $token = $operatorRepository->createTokenFor($operator, $params['zone']);

            return $this->respondWithData($token);
        }
        throw new HttpForbiddenException($this->request, 'Invalid credentials');
    }

    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respondWithData(null, 405);
    }

    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respondWithData(null, 405);
    }
    public function destroy(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respondWithData(null, 405);
    }
}
