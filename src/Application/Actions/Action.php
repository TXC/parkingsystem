<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainRecordNotFoundException;
use TXC\Box\Actions\DefaultAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action extends DefaultAction
{
    protected array $args;

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        ...$args
    ): ResponseInterface {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    protected function action(): ResponseInterface
    {
        throw new HttpNotFoundException($this->request);
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }
}
