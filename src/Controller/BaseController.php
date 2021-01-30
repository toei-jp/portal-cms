<?php

namespace App\Controller;

use App\Responder;
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
        $this->data->set('user', $this->auth->getUser());
        $this->data->set('alerts', $this->flash->getMessage('alerts'));
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

        return Responder\BaseResponder::factory($name, $this->view);
    }
}
