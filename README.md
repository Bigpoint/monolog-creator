# Monolog-Creator

These classes provide a simple factory for creating preconfigured [monolog](https://github.com/Seldaek/monolog) logger objects.

Monolog-Creator supports only a few handlers, formatters and processors from monolog at the moment. So feel free to extend the library.

### installation

```
composer require bigpoint/monolog-creator
```

### examples

#### minimal

You have to configurate at least the `_default` logger and one handler.

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
