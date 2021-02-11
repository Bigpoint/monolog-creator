# Monolog-Creator

These classes provide a simple factory for creating preconfigured [monolog](https://github.com/Seldaek/monolog) logger objects.

Monolog-Creator supports only a few handlers, formatters and processors of monolog at the moment. So feel free to extend the library.

### installation

```
composer require bigpoint/monolog-creator
```

### examples

#### minimal

You have to configure at least the `_default` logger and one handler.

**config.json**
~~~ json
{
    "handler" : {
        "stream" : {
            "path" : "./app.log"
        }
    },

    "logger" : {
        "_default" : {
            "handler" : ["stream"],
            "level" : "WARNING"
        }
    }
}
~~~

**index.php**
~~~ php
<?php
require 'vendor/autoload.php';

$config = json_decode(
    file_get_contents('config.json'),
    true
);

$loggerFactory = new \MonologCreator\Factory($config);

$logger = $loggerFactory->createLogger('name');
$logger->addWarning('I am a warning');
?>
~~~

#### different logger

Also you can create different preconfigured loggers. For example with
another log level or handler.

**config.json**
~~~ json
{
    "handler" : {
        "stream" : {
            "path" : "./app.log"
        }
    },

    "logger" : {
        "_default" : {
            "handler" : ["stream"],
            "level" : "WARNING"
        },
        "test" : {
            "handler" : ["stream"],
            "level" : "DEBUG"
        }
    }
}
~~~

**index.php**
~~~ php
<?php
require 'vendor/autoload.php';

$config = json_decode(
    file_get_contents('config.json'),
    true
);

$loggerFactory = new \MonologCreator\Factory($config);

$logger = $loggerFactory->createLogger('test');
$logger->addDebug('I am a debug message');
?>
~~~

#### different formatter

You can configure log output with a formatter

**config.json**
~~~ json
{
    "handler" : {
        "stream" : {
            "path" : "./app.log",
            "formatter" : "logstash"
        }
    },
    "formatter" : {
        "logstash" : {
            "type" : "test-app"
        }
    },
    "logger" : {
        "_default" : {
            "handler" : ["stream"],
            "level" : "WARNING"
        },
        "test" : {
            "handler" : ["stream"],
            "level" : "DEBUG"
        }
    }
}
~~~

#### optional processors

You can optionally add processors to your logger

**config.json**
~~~ json
"logger" : {
    "test" : {
        "handler" : ["stream"],
        "processors" : ["web"],
        "level" : "DEBUG"
    }
}
~~~


### supported handler:

#### StreamHandler
**config.json**
~~~ json
"handler" : {
    "stream" : {
        "path" : "./app.log"
    }
}
~~~

#### UdpHandler (custom handler)
**config.json**
~~~ json
"handler" : {
    "udp" : {
        "host" : "192.168.42.42",
        "port" : "6666"
    }
}
~~~

#### RedisHandler (with [predis/predis](https://packagist.org/packages/predis/predis))
**config.json**
~~~ json
"handler" : {
    "redis" : {
        "url" : "tcp://192.168.42.43:6379",
        "key" : "redisLogKey",
    }
}
~~~


### supported formatter:

#### LogstashFormatter
**config.json**
~~~ json
"formatter" : {
    "logstash" : {
        "type" : "test-app"
    }
}
~~~

#### LineFormatter
All values are optional. The boolean values `includeStacktraces`, `allowInlineLineBreaks`
and `ignoreEmptyContextAndExtra` can be `"true"` or `"false"`.

**config.json**
~~~ json
"formatter" : {
    "line" : {
        "format" : "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
        "dateFormat" : "Y-m-d H:i:s",
        "includeStacktraces" : "true",
        "allowInlineLineBreaks" : "true",
        "ignoreEmptyContextAndExtra" : "true"
    }
}
~~~


### supported processors:

#### WebProcessors
**config.json**
~~~ json
"logger" : {
    "test" : {
        "processors" : ["web"],
    }
}
~~~

#### RequestID Processor
Injects a random UUID for each request to make multiple log messages from the same request easier to follow. 

**config.json**
~~~ json
"logger" : {
    "test" : {
        "processors" : ["requestId"],
    }
}
~~~

#### ExtraFieldProcessor
Allows you to add high-level or specific fields to the logging data apart from the "ctxt_"-annotated ones. Those fields can be set using the _extraFields_ key in the logger configuration.

**config.json**
~~~ json
"logger" : {
    "test" : {
        "processors" : ["extraField"],
        "extraFields" : [
            "extra_key1" : "extra_value1",
            "extra_key2" : "extra_value2"
        ],
    }
}
~~~

## License & Authors
- Authors:: Peter Ahrens (<pahrens@bigpoint.net>), Andreas Schleifer (<aschleifer@bigpoint.net>), Hendrik Meyer (hmeyer@bigpoint.net)
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
