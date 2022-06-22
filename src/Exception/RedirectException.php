<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Psr\Http\Message\UriInterface;

class RedirectException extends Exception
{
    /** @var string|UriInterface */
    protected $url;

    protected ?int $status = null;

    /**
     * @param string|UriInterface $url The redirect destination.
     */
    public function __construct($url, ?int $status = null)
    {
        $this->url    = $url;
        $this->status = $status;

        parent::__construct('redirect');
    }

    /**
     * @return string|UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
}
