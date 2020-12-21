<?php

/**
 * AdvanceTicketResponder.php
 */

namespace Toei\PortalAdmin\Responder;

use Slim\Collection;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * AdvanceTicket responder
 */
class AdvanceTicketResponder extends BaseResponder
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
        return $this->view->render($response, 'advance_ticket/list.html.twig', $data->all());
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
        return $this->view->render($response, 'advance_ticket/new.html.twig', $data->all());
    }

    /**
     * edit
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function edit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'advance_ticket/edit.html.twig', $data->all());
    }
}
