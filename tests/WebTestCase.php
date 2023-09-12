<?php

declare(strict_types=1);

namespace App\Tests;

use TXC\Box\Testing\WithApplication;
use TXC\Box\Testing\WithContainer;
use TXC\Box\Testing\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class WebTestCase extends TestCase
{
    use WithContainer;
    use WithApplication;
    use WithEntityManager;

    protected function createRequest(string $method, string $uri, array $serverParams = []): ServerRequestInterface
    {
        /** @var ServerRequestFactoryInterface $factory */
        $factory = $this->getContainer()->get(ServerRequestFactoryInterface::class);

        return $factory->createServerRequest($method, $uri, $serverParams);
    }
}
