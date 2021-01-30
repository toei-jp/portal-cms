<?php

namespace App\Responder;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Collection;

/**
 * TheaterMeta responder
 */
class TheaterMetaResponder extends BaseResponder
{
    /**
     * opening hour
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function openingHour(Response $response, Collection $data)
    {
        return $this->view->render($response, 'theater_meta/opening_hour/list.html.twig', $data->all());
    }

    /**
     * opening hour edit
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function openingHourEdit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'theater_meta/opening_hour/edit.html.twig', $data->all());
    }
}
