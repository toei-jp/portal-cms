<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

abstract class AbstractMiddleware
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
