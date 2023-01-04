<?php

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'handler' => [
        'stream' => [
            'path'      => 'php://stdout',
            // 'formatter' => 'json',
        ],
        'udp' => [
            'host'      => 'localhost',
            'port'      => '9999',
            'formatter' => 'logstash'
        ]
    ],
    'formatter' => [
        'json'     => [],
        'logstash' => [
            'type' => 'app-dev'
        ],
    ],
    'logger' => [
        '_default' => [
            'handler'   => [
                'stream',
                // 'udp',
            ],
            'level'     => 'DEBUG',
        ],
    ]
];

$loggerFactory = new \MonologCreator\Factory($config);
$logger = $loggerFactory->createLogger('test');

\Monolog\ErrorHandler::register($logger);

$logger->warning('test', ['fu' => 'bar']);
