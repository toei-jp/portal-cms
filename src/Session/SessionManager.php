<?php

declare(strict_types=1);

namespace App\Session;

use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\Container;
use Laminas\Session\SessionManager as Base;

class SessionManager extends Base
{
    /** @var Container[] */
    protected array $containers = [];

    public function __construct(ConfigInterface $config)
    {
        parent::__construct($config);

        Container::setDefaultManager($this);
    }

    public function getContainer(string $name = 'default'): Container
    {
        if (! isset($this->containers[$name])) {
            $this->containers[$name] = new Container($name);
        }

        return $this->containers[$name];
    }
}
