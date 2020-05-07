<?php

/**
 * NewsResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder;

use Slim\Collection;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * News responder
 */
class NewsResponder extends BaseResponder
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
        return $this->view->render($response, 'news/list.html.twig', $data->all());
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
        return $this->view->render($response, 'news/new.html.twig', $data->all());
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
        return $this->view->render($response, 'news/edit.html.twig', $data->all());
    }
    
    /**
     * publication
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function publication(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/publication.html.twig', $data->all());
    }
}
