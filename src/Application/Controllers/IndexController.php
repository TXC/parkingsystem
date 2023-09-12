<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController extends Action
{
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        //return $this->respondWithData(null, 405);
        $response->getBody()->write('Hello :)');
        return $response;
    }

    public function store(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        //$this->setRequestResponse($request, $response);
        return $this->respondWithData(null, 405);
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
