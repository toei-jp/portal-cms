<?php

namespace App\Responder;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Collection;

/**
 * Auth responder
 */
class AuthResponder extends BaseResponder
{
    /**
     * login
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function login(Response $response, Collection $data)
    {
        return $this->view->render($response, 'auth/login.html.twig', $data->all());
    }
}
