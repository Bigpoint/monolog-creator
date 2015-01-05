# Monolog-Creator

This classes provides a simple factory for creating pre configurated [monolog](https://github.com/Seldaek/monolog) logger objects.

Monolog-Creator supports not much handler, formatter and processor from monolog at the moment. So feel free to extend the library.

### examples

##### minimal

You have to configurate at least the _default logger and one handler.

**config.json**
```json
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
```php
$config = json_decode(
    file_get_contents('config.json'),
    true
);

$loggerFactory = new \Logger\Factory($config);

$logger = $loggerFactory->createLogger();
$logger->addWarning('I am a warning');
```

##### different logger

But you can also create different configurated logger. For example with
another log level or handler.

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

```
$loggerFactory = new \Logger\Factory($config);

$logger = $loggerFactory->createLogger('test');
$logger->addDebug('I am a debug message');
```

##### different formatter

You can configure the log output at handle via formatter

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
            "type" : "partner-integration-televisa"
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

##### optional processors

You can optionally add processors to your logger

```
{
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

##### StreamHandler
```
"handler" : {
    "stream" : {
        "path" : "./app.log"
    }
}
```

##### UdpHandler (custom handler)
```
"handler" : {
    "udp" : {
        "host"       : "192.168.50.48",
        "port"       : "9999"
    }
}
```


### supported formatter:

##### LogstashFormatter
```
"formatter" : {
    "logstash" : {
        "type" : "partner-integration-televisa"
    }
}
```


### supported processors:

##### WebProcessors

Adds the current request URI, request method, client IP and user agent to a log record.
