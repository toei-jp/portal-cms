<?php

/**
 * EditorResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Editor responder
 */
class EditorResponder extends BaseResponder
{
    /**
     * upload
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function upload(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }
}
