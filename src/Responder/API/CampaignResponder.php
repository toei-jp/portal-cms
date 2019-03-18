<?php
/**
 * CampaignResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

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
