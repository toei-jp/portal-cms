<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MotionPictureExtenstion extends AbstractExtension
{
    protected string $apiEndpoint;
    protected string $apiProjectId;

    /**
     * @param array<string, mixed> $settings
     */
    public function __construct(array $settings)
    {
        $this->apiEndpoint  = $settings['api_endpoint'];
        $this->apiProjectId = $settings['api_project_id'];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('mp_api_endpoint', [$this, 'getApiEndpoint']),
            new TwigFunction('mp_api_project_id', [$this, 'getApiProjectId']),
        ];
    }

    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    public function getApiProjectId(): string
    {
        return $this->apiProjectId;
    }
}
