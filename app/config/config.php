<?php
use Phalcon\Config;
use Phalcon\Logger;

return new Config([

    'site' => [
        'name'        => 'Phalcon-Backend',
        'description' => 'Phalcon-Backend',
        'keywords'    => 'Phalcon-Backend',
        'email' => '1079087531@qq.com'
    ],

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'suhua123',
        'dbname'   => 'phalcon_backend',
        'charset'  => 'utf8'
    ],

    'metadata' => [
        'adapter'     => 'Files',
        'metaDataDir' => BASE_DIR . 'app/cache/metaData/',
    ],

    'application' => [
        'controllersDir' => BASE_DIR . 'app/controllers/',
        'modelsDir'      => BASE_DIR . 'app/models/',
        'viewsDir'       => BASE_DIR . 'app/views/',
        'pluginsDir'     => BASE_DIR . 'app/plugins/',
        'libraryDir'     => BASE_DIR . 'app/library/',
        'staticBaseUri' => '/',
        'baseUri'       => '/',
        'debug' => false
    ],

    'volt' => [
        'compiledExt'  => '.php',
        'separator'    => '_',
        'cacheDir'     => BASE_DIR . 'app/cache/volt/',
        'forceCompile' => false,
    ],

    'dataCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'gsms-data-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/data/',
    ],

    'modelsCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'gsms-models-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/models/',
    ],

    'viewCache' => [
        'backend'  => 'File',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'gsms-views-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/views/',
    ],

    'session' => [
        'adapter' => 'Files',
    ],

    'logger' => [
        'path'     => BASE_DIR . 'app/logs/',
        'format'   => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
        'date'     => 'Y-m-d H:i:s', // Y-m-d H:i:s
        'logLevel' => Logger::WARNING,
        'filename' => 'application.log',
    ],

    'error' => [
        'logger'    => BASE_DIR . 'app/logs/error.log',
        'formatter' => [
            'format' => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
            'date'   => 'Y-m-d H:i:s',
        ],
        'controller' => 'error',
        'action'     => 'index',
    ],
]);
