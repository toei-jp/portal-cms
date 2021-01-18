<?php

namespace App\Responder;

use Slim\Collection;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Index responder
 */
class IndexResponder extends BaseResponder
{
    /**
     * index
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function index(Response $response, Collection $data)
    {
        return $this->view->render($response, 'index/index.html.twig', $data->all());
    }
}
