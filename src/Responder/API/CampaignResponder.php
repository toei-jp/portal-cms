<?php

namespace App\Responder\API;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Campaign responder
 */
class CampaignResponder extends BaseResponder
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
}
