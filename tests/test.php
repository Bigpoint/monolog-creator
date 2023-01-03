<?php

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'handler' => [
        'stream' => [
            'path' => 'php://stdout',
        ],
    ],
    'logger' => [
        '_default' => [
            'handler' => ['stream'],
            'level'   => 'DEBUG',
        ],
    ]
];

$loggerFactory = new \MonologCreator\Factory($config);
$logger = $loggerFactory->createLogger('test');

\Monolog\ErrorHandler::register($logger);

$logger->warning('test', ['fu' => 'bar']);
