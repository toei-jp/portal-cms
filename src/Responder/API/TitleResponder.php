<?php
/**
 * TitleResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder\API;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

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
