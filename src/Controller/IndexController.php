<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends BaseController
{
    /**
     * index action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeIndex(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'index/index.html.twig');
    }
}
