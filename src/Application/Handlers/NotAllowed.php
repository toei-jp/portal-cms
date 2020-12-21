<?php

namespace Toei\PortalAdmin\Application\Handlers;

use Slim\Handlers\NotAllowed as BaseHandler;
use Slim\Views\Twig;

/**
 * NotAllowed handler
 */
class NotAllowed extends BaseHandler
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

    protected function renderHtmlNotAllowedMessage($methods)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlNotAllowedMessage($methods);
        }

        return $this->view->fetch('error/client.html.twig', [
            'title' => 'Method Not Allowed',
            'status' => 405,
            'message' => 'このリクエストは許可されていません。',
        ]);
    }
}
