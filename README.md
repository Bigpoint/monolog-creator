# Logger

This logger provides a factory for monolog loggers, which are configurable via
array configuration.

## example configuration

```
"monolog": {
    "handler" : {
        "stream" : {
            "path" : "./app.log",
            "level" : "INFO"
        },
        "udp" : {
            "host"       : "192.168.50.48",
            "port"       : "9999",
            "level"      : "INFO",
            "formatter"  : "logstash"
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
        }
        "integration" : {
            "handler" : ["stream", "udp"],
            "level" : "INFO"
        },
        "caller" : {
            "handler" : ["stream", "udp"],
            "level" : "INFO"
        },
        "detector" : {
            "handler" : ["stream", "udp"],
            "level" : "INFO"
        },
        "controller" : {
            "handler" : ["stream", "udp"],
            "level" : "INFO"
        }
    }
}
```
