<?php
/**
 * AuthResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

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
