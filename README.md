# Logger

This logger provides a factory for creating [monolog](https://github.com/Seldaek/monolog) logger objects. The composition of monologs handler and formatter is configurable via an configuration array.

The Factory wraps not much handler and formatter from monolog at the moment. So feel free to extend the library.

### examples

##### minimal

You have to configurate at least the _default logger and one handler.

```
{
    "handler" : {
        "stream" : {
            "path" : "./app.log",
            "level" : "INFO"
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

```
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
            "path" : "./app.log",
            "level" : "INFO"
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
            "level" : "INFO",
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


### supported handler:

##### StreamHandler
```
"handler" : {
    "stream" : {
        "path" : "./app.log",
        "level" : "INFO"
    }
}
```

##### UdpHandler (custom handler)
```
"handler" : {
    "udp" : {
        "host"       : "192.168.50.48",
        "port"       : "9999",
        "level"      : "INFO"
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
