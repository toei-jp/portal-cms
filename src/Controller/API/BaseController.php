<?php

namespace App\Controller\API;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controller\AbstractController;
use App\Responder;
use App\Responder\API as ApiResponder;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * pre execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function preExecute($request, $response): void
    {
    }

    /**
     * post execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function postExecute($request, $response): void
    {
    }

    /**
     * get responder
     *
     * @return Responder\AbstractResponder
     */
    protected function getResponder(): Responder\AbstractResponder
    {
        $path = explode('\\', static::class);
        $name = str_replace('Controller', '', array_pop($path));

        return ApiResponder\BaseResponder::factory($name);
    }
}
