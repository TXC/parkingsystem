<?php

declare(strict_types=1);

use App\Application\Controllers\IndexController;
use App\Application\Controllers\LoginController;
use App\Application\Controllers\OperatorController;
use App\Application\Controllers\ParkingController;
use App\Application\Controllers\TicketController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // Set default route strategy.
    //$app->getRouteCollector()->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponse());
    //$app->getRouteCollector()->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs());

    $app->post('/login', [LoginController::class, 'store'])
        ->setName('auth.login');

    $app->get('/logout', [LoginController::class, 'index'])
        ->setName('auth.logout');

    $app->group('/parking', function (Group $group) {
        $group->get('/validate/{license}', [OperatorController::class, 'show'])
              ->setName('operator.check');

        $group->post('/{zone}', [ParkingController::class, 'store'])
              ->setName('parking.create');
    });

    $app->group('/ticket', function (Group $group) {
        $group->get('/{ticketId}', [TicketController::class, 'show'])
              ->setName('ticket.lookup');
        $group->patch('/{ticketId}', [TicketController::class, 'update'])
              ->setName('ticket.payment');
    });

    $app->get('/', [IndexController::class, 'index'])->setName('root');

    /*
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handlers
        //$handler = $this->notFoundHandler;
        //return $handler($request, $response);
        return $response; //->withStatus(403);
    });
    */
};
