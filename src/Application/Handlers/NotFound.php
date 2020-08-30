<?php

/**
 * NotFound.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Application\Handlers;

use Slim\Handlers\NotFound as BaseHandler;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

/**
 * NotFound handler
 */
class NotFound extends BaseHandler
{
    /** @var Twig */
    protected $view;

    /**
     * construct
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlNotFoundOutput($request);
        }

        return $this->view->fetch('error/client.html.twig', [
            'title' => 'Not Found',
            'status' => 404,
            'message' => 'このページは存在しません。',
        ]);
    }
}
