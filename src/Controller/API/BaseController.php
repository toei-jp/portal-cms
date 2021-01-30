<?php

namespace App\Controller\API;

use App\Controller\AbstractController;
use App\Responder;
use App\Responder\API as ApiResponder;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * pre execute
     *
     * @param Request  $request
     * @param Response $response
     * @return void
     */
    protected function preExecute($request, $response): void
    {
    }

    /**
     * post execute
     *
     * @param Request  $request
     * @param Response $response
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
