<?php

/**
 * container.php
 *
 * AbstractControllerのphpdoc更新を推奨。
 *
 * @see App\Controller\AbstractController\__call()
 */

use App\Application\Handlers\Error;
use App\Application\Handlers\NotAllowed;
use App\Application\Handlers\NotFound;
use App\Application\Handlers\PhpError;
use App\Auth;
use App\Logger\DbalLogger;
use App\Logger\Handler\AzureBlobStorageHandler;
use App\Session\SessionManager;
use App\Twig\Extension\AzureStorageExtension;
use App\Twig\Extension\MotionPictureExtenstion;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Laminas\Session\Config\SessionConfig;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Slim\App as SlimApp;
use Slim\Flash\Messages as FlashMessages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
/** @var SlimApp $app */
// phpcs:enable

$container = $app->getContainer();

/**
 * view
 *
 * @link https://www.slimframework.com/docs/v3/features/templates.html
 *
 * @return Twig
 */
$container['view'] = static function ($container) {
    $settings = $container->get('settings')['view'];

    $view = new Twig($settings['template_path'], $settings['settings']);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri    = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    // add Extension
    $view->addExtension(new DebugExtension());
    $view->addExtension(new AzureStorageExtension(
        $container->get('bc'),
        $container->get('settings')['storage']['public_endpoint']
    ));
    $view->addExtension(new MotionPictureExtenstion(
        $container->get('settings')['mp']
    ));

    return $view;
};

/**
 * logger
 *
 * @link https://github.com/Seldaek/monolog
 *
 * @return Logger
 */
$container['logger'] = static function ($container) {
    $settings = $container->get('settings')['logger'];

    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new PsrLogMessageProcessor());
    $logger->pushProcessor(new UidProcessor());
    $logger->pushProcessor(new IntrospectionProcessor());
    $logger->pushProcessor(new WebProcessor());
    $logger->pushProcessor(new MemoryUsageProcessor());
    $logger->pushProcessor(new MemoryPeakUsageProcessor());

    if (isset($settings['chrome_php'])) {
        $chromePhpSettings = $settings['chrome_php'];
        $logger->pushHandler(new ChromePHPHandler(
            $chromePhpSettings['level']
        ));
    }

    $azureBlobStorageSettings = $settings['azure_blob_storage'];
    $azureBlobStorageHandler  = new AzureBlobStorageHandler(
        $container->get('bc'),
        $azureBlobStorageSettings['container'],
        $azureBlobStorageSettings['blob'],
        $azureBlobStorageSettings['level']
    );

    $fingersCrossedSettings = $settings['fingers_crossed'];
    $logger->pushHandler(new FingersCrossedHandler(
        $azureBlobStorageHandler,
        $fingersCrossedSettings['activation_strategy']
    ));

    return $logger;
};

/**
 * Doctrine entity manager
 *
 * @return EntityManager
 */
$container['em'] = static function ($container) {
    $settings = $container->get('settings')['doctrine'];

    /**
     * 第５引数について、他のアノテーションとの競合を避けるためSimpleAnnotationReaderは使用しない。
     *
     * @Entity => @ORM\Entity などとしておく。
     */
    $config = Setup::createAnnotationMetadataConfiguration(
        $settings['metadata_dirs'],
        $settings['dev_mode'],
        null,
        null,
        false
    );

    $config->setProxyDir(APP_ROOT . '/src/ORM/Proxy');
    $config->setProxyNamespace('App\ORM\Proxy');
    $config->setAutoGenerateProxyClasses($settings['dev_mode']);

    $logger = new DbalLogger($container->get('logger'));
    $config->setSQLLogger($logger);

    return EntityManager::create($settings['connection'], $config);
};

/**
 * session manager
 *
 * @return SessionManager
 */
$container['sm'] = static function ($container) {
    $settings = $container->get('settings')['session'];

    $config = new SessionConfig();
    $config->setOptions($settings);

    return new SessionManager($config);
};

/**
 * Flash Messages
 *
 * @return FlashMessages
 */
$container['flash'] = static function ($container) {
    $session = $container->get('sm')->getContainer('flash');

    return new FlashMessages($session);
};

/**
 * auth
 *
 * @return Auth
 */
$container['auth'] = static function ($container) {
    return new Auth($container);
};

/**
 * Azure Blob Storage Client
 *
 * @link https://github.com/Azure/azure-storage-php/tree/master/azure-storage-blob
 *
 * @return BlobRestProxy
 */
$container['bc'] = static function ($container) {
    $settings   = $container->get('settings')['storage'];
    $protocol   = $settings['secure'] ? 'https' : 'http';
    $connection = sprintf(
        'DefaultEndpointsProtocol=%s;AccountName=%s;AccountKey=%s;',
        $protocol,
        $settings['account_name'],
        $settings['account_key']
    );

    if ($settings['blob_endpoint']) {
        $connection .= sprintf('BlobEndpoint=%s;', $settings['blob_endpoint']);
    }

    return BlobRestProxy::createBlobService($connection);
};

$container['errorHandler'] = static function ($container) {
    return new Error(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['phpErrorHandler'] = static function ($container) {
    return new PhpError(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['notFoundHandler'] = static function ($container) {
    return new NotFound(
        $container->get('view')
    );
};

$container['notAllowedHandler'] = static function ($container) {
    return new NotAllowed(
        $container->get('view')
    );
};
