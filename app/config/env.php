<?php
use Dotenv\Dotenv;

define('BASE_DIR', realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR);

include BASE_DIR . 'vendor/autoload.php';

$dotenv = new Dotenv(realpath(BASE_DIR));
$dotenv->load();

//环境变量
define('ENV_PRODUCTION', 'production');
define('ENV_STAGING', 'staging');
define('ENV_DEVELOPMENT', 'development');
define('ENV_TESTING', 'testing');

define('APPLICATION_ENV', getenv('APP_ENV') ?: ENV_PRODUCTION);
define('APP_START_TIME', microtime(true));
define('APP_START_MEMORY', memory_get_usage());
define('NAMESPACE_SEPARATOR', '\\');
define('DEV_IP', '192.168.');
define('HOSTNAME', explode('.', gethostname())[ 0 ]);

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

if (function_exists('mb_substitute_character')) {
    mb_substitute_character('none');
}

if (ENV_PRODUCTION === APPLICATION_ENV) {
    header_remove('X-Powered-By');
    error_reporting(E_ALL ^ E_NOTICE);

    if (PHP_VERSION_ID >= 70000) {
        ini_set('zend.assertions', -1);
    }
}

if (ENV_DEVELOPMENT === APPLICATION_ENV) {
    error_reporting(E_ALL);

    if (extension_loaded('xdebug')) {
        ini_set('xdebug.collect_params', 4);
    }
}

if (PHP_SAPI == 'cli') {
    set_time_limit(0);
}
