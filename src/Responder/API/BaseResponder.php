<?php

/**
 * BaseResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Responder\API;

use Toei\PortalAdmin\Responder\AbstractResponder;

/**
 * Base responder
 */
abstract class BaseResponder extends AbstractResponder
{
    /**
     * factory
     *
     * @param string $name
     * @return AbstractResponder
     */
    final public static function factory(string $name): AbstractResponder
    {
        $className =  __NAMESPACE__  . '\\' . $name . 'Responder';
        
        return new $className();
    }
    
    /**
     * contsruct
     */
    public function __construct()
    {
    }
}
