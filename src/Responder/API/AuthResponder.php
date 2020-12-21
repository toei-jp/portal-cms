<?php

/**
 * AuthResponder.php
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Auth responder
 */
class AuthResponder extends BaseResponder
{
    /**
     * token
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function token(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }
}
