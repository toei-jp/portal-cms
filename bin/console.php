<?php

/**
 * @link https://github.com/symfony/console
 */

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

/** @var \Slim\App $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';
$container = $app->getContainer();

$application = new Application();
$application->run();
