<?php

namespace Toei\PortalAdmin\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * MotionPicture twig extension class
 */
class MotionPictureExtenstion extends AbstractExtension
{
    /** @var array */
    protected $settings;

    /**
     * construct
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * get functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [new TwigFunction('mp_api_endpoint', [$this, 'getApiEndpoint'])];
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
