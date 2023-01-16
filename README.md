# Monolog-Creator

These classes provide a simple factory for creating pre-configured [monolog](https://github.com/Seldaek/monolog) logger objects.

Monolog-Creator supports only a few handlers, formatters and processors of monolog at the moment. So feel free to extend the library.

See the [changelog](/changelog.md) for the updates in between the major versions.

### installation

```
composer require bigpoint/monolog-creator
```

### examples

#### minimal

You have to configure at least the `_default` logger and one handler.

**index.php**
~~~ php
<?php
require 'vendor/autoload.php';

$config = [
    "handler" => [
        "stream" => [
            "path" => "php://stdout",
        ],
    ],
    "logger" => [
        "_default" => [
            "handler" => ["stream"],
            "level"   => "WARNING",
        ],
    ],
];

$loggerFactory = new \MonologCreator\Factory($config);

$logger = $loggerFactory->createLogger('name');
$logger->addWarning('I am a warning');
?>
~~~

#### different logger

Also you can create different pre-configured loggers. For example with
another log level or handler.

**index.php**
~~~ php
<?php
require 'vendor/autoload.php';

$config = [
    "handler" => [
        "stream" => [
            "path" => "php://stdout",
        ],
    ],
    "logger" => [
        "_default" => [
            "handler" => ["stream"],
            "level"   => "WARNING",
        ],
        "test" => [
            "handler" => ["stream"],
            "level"   => "DEBUG",
        ],
    ],
];

$loggerFactory = new \MonologCreator\Factory($config);

$logger = $loggerFactory->createLogger('test');
$logger->addDebug('I am a debug message');
?>
~~~

#### different formatter

You can configure log output with a formatter. Be aware that a formatter has a general config under the `formatter` key and it has to be assigned to specific handler in the `handler`section.

~~~ php
$config = [
    "handler" => [
        "stream" => [
            "path"      => "php://stdout",
            "formatter" => "json",
        ],
    ],
    'formatter' => [
        'json'     => [],
    ],
    "logger" => [
        "_default" => [
            "handler" => ["stream"],
            "level"   => "WARNING",
        ],
        "test" => [
            "handler" => ["stream"],
            "level"   => "DEBUG",
        ],
    ],
];
~~~

#### optional processors

You can optionally add processors to your logger

~~~ php
$config = [
    "logger" => [
        "test" => [
            "handler" => ["stream"],
            "processors" : ["web"],
            "level"   => "DEBUG",
        ],
    ],
];
~~~


### supported handler:

#### StreamHandler
~~~ php
$config = [
    "handler" => [
        "stream" => [
            "path"      => "php://stdout",
        ],
    ],
];
~~~

#### UdpHandler (custom handler)
~~~ php
$config = [
    "handler" => [
        'udp' => [
            'host'      => 'localhost',
            'port'      => '9999',
        ],
    ],
];
~~~

#### RedisHandler (with [predis/predis](https://packagist.org/packages/predis/predis))
To use the Redis handler, you have to create the Predis client object yourself and set it to the Factory, before creating any logger.
~~~ php
$config = [
    "handler" => [
        'redis' => [
            "key" => "php_logs",
        ]
    ],
];

$predisClient = new \Predis\Client('tcp://192.168.42.43:6379');
$loggerFactory->setPredisClient($predisClient);
~~~

### supported formatter:

#### JsonFormatter
Currently no options are supported here.
~~~ php
$config = [
    'formatter' => [
        'json'     => [],
    ],
];
~~~

#### LineFormatter
All values are optional. The boolean values `includeStacktraces`, `allowInlineLineBreaks`
and `ignoreEmptyContextAndExtra` can be `"true"` or `"false"`.

~~~ php
$config = [
    'formatter' => [
        'line' => [
            "format"                     => "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            "dateFormat"                 => "Y-m-d H:i:s",
            "includeStacktraces"         => "true",
            "allowInlineLineBreaks"      => "true",
            "ignoreEmptyContextAndExtra" => "true",
        ],
    ],
];
~~~

#### LogstashFormatter
~~~ php
$config = [
    'formatter' => [
        'logstash'     => [
            "type" => "test-app"
        ],
    ],
];
~~~

### supported processors:

#### WebProcessors
~~~ php
$config = [
    'logger' => [
        '_default' => [
            'handler'   => [
                'stream',
            ],
            "processors"  => ["web"],
            'level' => 'DEBUG',
        ],
    ]
];
~~~

#### RequestID Processor
Injects a random UUID for each request to make multiple log messages from the same request easier to follow.

~~~ php
$config = [
    'logger' => [
        '_default' => [
            'handler'   => [
                'stream',
            ],
            "processors"  => ["requestId"],
            'level' => 'DEBUG',
        ],
    ]
];
~~~

#### ExtraFieldProcessor
Allows you to add high-level or specific fields to the logging data apart from the `context` list. These additional fields will be present in the `extra` list in the output.

~~~ php
$config = [
    'logger' => [
        '_default' => [
            'handler'   => [
                'stream',
            ],
            "processors"  => ["extraFields"],
            "extraFields" : [
                "extra_key1" : "extra_value1",
                "extra_key2" : "extra_value2"
            ],
            'level' => 'DEBUG',
        ],
    ]
];
~~~

## License & Authors
- Authors:: Peter Ahrens, Andreas Schleifer (<aschleifer@bigpoint.net>), Hendrik Meyer
- Contributors:: Sebastian Götze (<sgoetze@bigpoint.net>)

```text
Copyright:: 2015-2021 Bigpoint GmbH

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
