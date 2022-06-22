<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MotionPictureExtenstion extends AbstractExtension
{
    /** @var array<string, mixed> */
    protected array $settings;

    /**
     * @param array<string, mixed> $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [new TwigFunction('mp_api_endpoint', [$this, 'getApiEndpoint'])];
    }

    public function getApiEndpoint(): string
    {
        return $this->settings['api_endpoint'];
    }
}
