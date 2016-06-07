<?php
use Phalcon\Config;
use Phalcon\Logger;

return new Config([
    'metadata' => [
        'adapter' => 'Memory',
    ],
    'dataCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'modelsCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'viewCache' => [
        'backend' => 'Memory',
    ],
    'logger' => [
        'logLevel' => Logger::SPECIAL,
    ],
    'application' => [
        'debug' => true
    ],
    'volt' => [
        'forceCompile' => true,
    ]
]);
