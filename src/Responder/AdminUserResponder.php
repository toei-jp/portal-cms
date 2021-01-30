<?php

namespace App\Responder;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Collection;

/**
 * AdminUser responder
 */
class AdminUserResponder extends BaseResponder
{
    /**
     * list
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function list(Response $response, Collection $data)
    {
        return $this->view->render($response, 'admin_user/list.html.twig', $data->all());
    }

    /**
     * new
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function new(Response $response, Collection $data)
    {
        return $this->view->render($response, 'admin_user/new.html.twig', $data->all());
    }
}
