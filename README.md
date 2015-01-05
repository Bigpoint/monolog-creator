# Monolog-Creator

This classes provides a simple factory for creating preconfigurated [monolog](https://github.com/Seldaek/monolog) logger objects.

Monolog-Creator supports not much handler, formatter and processor from monolog at the moment. So feel free to extend the library.

### examples

#### minimal

You have to configurate at least the _default logger and one handler.

**config.json**
```
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
```

**index.php**
```
<\?php
$config = json_decode(
    file_get_contents('config.json'),
    true
);

$loggerFactory = new \Logger\Factory($config);

$logger = $loggerFactory->createLogger();
$logger->addWarning('I am a warning');
```

#### different logger

Also you can create different preconfigurated logger. For example with
another log level or handler.

**config.json**
```
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
```

**index.php**
```
<\?php

$config = json_decode(
    file_get_contents('config.json'),
    true
);

$loggerFactory = new \Logger\Factory($config);

$logger = $loggerFactory->createLogger('test');
$logger->addDebug('I am a debug message');
```

#### different formatter

You can configure log output with a formatter

**config.json**
```
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
```

#### optional processors

You can optionally add processors to your logger

**config.json**
```
{
    ...
    "logger" : {
        "test" : {
            "handler" : ["stream"],
            "processors" : ["web"],
            "level" : "DEBUG"
        }
    }
}
```


### supported handler:

#### StreamHandler
**config.json**
```
{
    ...
    "handler" : {
        "stream" : {
            "path" : "./app.log"
        }
    }
}
```

#### UdpHandler (custom handler)
**config.json**
```
{
    ...
    "handler" : {
        "udp" : {
            "host" : "192.168.42.42",
            "port" : "6666"
        }
    }
}
```


### supported formatter:

#### LogstashFormatter
**config.json**
```
{
    ...
    "formatter" : {
        "logstash" : {
            "type" : "test-app"
        }
    }
}
```


### supported processors:

#### WebProcessors

**config.json**
```
{
    ...
    "logger" : {
        "test" : {
            ...
            "processors" : ["web"],
        }
    }
}
```
