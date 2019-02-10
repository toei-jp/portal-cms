<?php
/**
 * BaseResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder;

use Slim\Views\Twig;

/**
 * Base responder
 */
abstract class BaseResponder extends AbstractResponder
{
    /** @var Twig view */
    protected $view;
    
    /**
     * contsruct
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }
}
