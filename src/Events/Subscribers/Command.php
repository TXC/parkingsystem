<?php

declare(strict_types=1);

namespace App\Events\Subscribers;

use App\Events\Listeners;
use League\Event\ListenerRegistry;
use League\Event\ListenerSubscriber;
use Psr\Container\ContainerInterface;

class Command implements ListenerSubscriber
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function subscribeListeners(ListenerRegistry $acceptor): void
    {
        $acceptor->subscribeTo('setup.complete', new Listeners\SetupComplete($this->container));
    }
}
