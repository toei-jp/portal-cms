<?php

/**
 * settings.php
 *
 * @return array
 */

$settings = [];

$settings['displayErrorDetails']    = APP_DEBUG;
$settings['addContentLengthHeader'] = false;

// view
$settings['view'] = [
    'template_path' => APP_ROOT . '/template',
    'settings' => [
        'debug' => APP_DEBUG,
        'cache' => APP_ROOT . '/cache/view',
    ],
];

/**
 * session
 *
 * laminas-session configのオプションとして使用。
 *
 * @link https://docs.laminas.dev/laminas-session/config/
 */
$settings['session'] = ['name' => 'toei_admin'];


// logger
$getLoggerSetting = function () {
    $settings = ['name' => 'app'];

    if (APP_DEBUG) {
        $settings['chrome_php'] = [
            'level' => \Monolog\Logger::DEBUG,
        ];
    }

    $settings['fingers_crossed'] = [
        'activation_strategy' => \Monolog\Logger::ERROR,
    ];

    $settings['azure_blob_storage'] = [
        'level' => \Monolog\Logger::INFO,
        'container' => 'admin-log',
        'blob' => date('Ymd') . '.log',
    ];

    return $settings;
};

$settings['logger'] = $getLoggerSetting();

/**
 * doctrine
 *
 * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/configuration.html#installation-and-configuration
 *
 * @return array
 */
$getDoctrineSetting = function () {
    $settings = [
        /**
         * ビルドに影響するのでtrueにするのはローカルモードに限定しておく。
         *
         * trueの場合
         * * キャッシュはメモリ内で行われる（ArrayCache）
         * * Proxyオブジェクトは全てのリクエストで再作成される
         *
         * falseの場合
         * * 指定のキャッシュが使用されるかAPC、Xcache、Memcache、Redisの順で確認される
         * * Proxyクラスをコマンドラインから明示的に作成する必要がある
         */
        'dev_mode' => (APP_ENV === 'local'),

        'metadata_dirs' => [APP_ROOT . '/src/ORM/Entity'],

        'connection' => [
            'driver'   => 'pdo_mysql',
            'host'     => getenv('MYSQLCONNSTR_HOST'),
            'port'     => getenv('MYSQLCONNSTR_PORT'),
            'dbname'   => getenv('MYSQLCONNSTR_NAME'),
            'user'     => getenv('MYSQLCONNSTR_USER'),
            'password' => getenv('MYSQLCONNSTR_PASSWORD'),
            'charset'  => 'utf8mb4',
            'driverOptions'  => [],

            // @link https://m-p.backlog.jp/view/SASAKI-246
            'serverVersion' => '5.7',
        ],
    ];

    $cafile = getenv('MYSQLCONNSTR_SSL_CA');

    if ($cafile) {
        $settings['connection']['driverOptions'][PDO::MYSQL_ATTR_SSL_CA] = $cafile;
    }

    return $settings;
};

$settings['doctrine'] = $getDoctrineSetting();


// storage
$getStorageSettings = function () {
    $settings = [
        'account_name' => getenv('CUSTOMCONNSTR_STORAGE_NAME'),
        'account_key' => getenv('CUSTOMCONNSTR_STORAGE_KEY'),
    ];

    $settings['secure'] = (getenv('CUSTOMCONNSTR_STORAGE_SECURE') === 'false')
        ? false
        : true;

    $settings['blob_endpoint'] = (getenv('CUSTOMCONNSTR_STORAGE_BLOB_ENDPOINT'))
        ?: null;

    $settings['public_endpoint'] = (getenv('CUSTOMCONNSTR_STORAGE_PUBLIC_ENDOPOINT'))
        ?: null;

    return $settings;
};

$settings['storage'] = $getStorageSettings();


// API
$settings['api'] = [
    'auth_server' => getenv('APPSETTING_API_AUTH_SERVER'),
    'auth_client_id' => getenv('APPSETTING_API_AUTH_CLIENT_ID'),
    'auth_client_secret' => getenv('APPSETTING_API_AUTH_CLIENT_SECRET'),
];

// MotionPicture
$settings['mp'] = [
    'api_endpoint' => getenv('APPSETTING_MP_API_ENDPOINT'),
];

return $settings;
