<?php

/**
 * TitleResponder.php
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Title responder
 */
class TitleResponder extends BaseResponder
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
        return $response->withJson($data->all());
    }

    /**
     * find imported title
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function findImported(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }

    /**
     * import titles
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function importTitles(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }

    /**
     * autocomplete
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function autocomplete(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }
}
