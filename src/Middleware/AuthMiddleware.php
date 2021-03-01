<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class AuthMiddleware extends AbstractMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $auth = $this->container->get('auth');

        if (! $auth->isAuthenticated()) {
            return $response->withRedirect(
                $this->container->get('router')->pathFor('login')
            );
        }

        $response = $next($request, $response);

        return $response;
    }
}
