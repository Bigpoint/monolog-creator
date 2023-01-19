<?php

/**
 * simple file to test the libraries features manually
 */

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
        ],
        'redis' => [
            "key" => "php_logs",
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
                'udp',
                // 'redis',
            ],
            "processors"  => ["requestId", "extraFields"],
            "extraFields" => [
                "extra_key1" => "extra_value1",
                "extra_key2" => "extra_value2"
            ],
            'level' => 'DEBUG',
        ],
    ]
];

$loggerFactory = new \MonologCreator\Factory($config);
// $predisClient  = new \Predis\Client('tcp://localhost:6379');
// $loggerFactory->setPredisClient($predisClient);

$logger = $loggerFactory->createLogger('test');

\Monolog\ErrorHandler::register($logger);

$logger->warning('test', ['fu' => 'bar']);
