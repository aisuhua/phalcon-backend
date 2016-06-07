<?php
use Backend\Bootstrap;

include_once realpath(dirname(dirname(__FILE__))) . '/app/config/env.php';
include_once BASE_DIR . 'app/library/Bootstrap.php';

$bootstrap = new Bootstrap();

if (APPLICATION_ENV == ENV_TESTING) {
    return $bootstrap->run();
} else {
    echo $bootstrap->run();
}
