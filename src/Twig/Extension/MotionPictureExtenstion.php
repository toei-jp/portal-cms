<?php
/**
 * MotionPictureExtenstion.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Twig\Extension;

use Psr\Container\ContainerInterface;

/**
 * MotionPicture twig extension class
 */
class MotionPictureExtenstion extends \Twig_Extension
{
    /** @var array */
    protected $settings;
    
    /**
     * construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->settings = $container->get('settings')['mp'];
    }
    
    /**
     * get functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('mp_api_endpoint', [$this, 'getApiEndpoint'])
        ];
    }
    
    /**
     * return API endpoint
     *
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return $this->settings['api_endpoint'];
    }
}
