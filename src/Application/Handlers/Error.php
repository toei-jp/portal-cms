<?php

/**
 * Error.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Application\Handlers;

use Monolog\Logger;
use Slim\Handlers\Error as BaseHandler;

/**
 * Error handler
 */
class Error extends BaseHandler
{
    /** @var Logger */
    protected $logger;

    /**
     * construct
     *
     * @param Logger $logger
     * @param bool $displayErrorDetails
     */
    public function __construct(Logger $logger, bool $displayErrorDetails = false)
    {
        $this->logger = $logger;

        parent::__construct($displayErrorDetails);
    }

    /**
     * @param \Exception|\Throwable $throwable
     * @return void
     * @see Slim\Handlers\AbstractError
     */
    protected function writeToErrorLog($throwable)
    {
        $this->log($throwable);
    }

    /**
     * @param \Exception|\Throwable $exception
     * @return void
     */
    protected function log($exception)
    {
        $this->logger->error($exception->getMessage(), [
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlErrorMessage(\Exception $exception)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlErrorMessage($exception);
        }

        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
