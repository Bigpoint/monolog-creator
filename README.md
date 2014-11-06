# Logger

This logger provides a factory for monolog loggers, which are configurable via
array configuration.

## example configuration

```
"monolog": {
    "handler" : {
        "file" : {
            "path" : "./app.log",
            "level" : "INFO"
        },
        "udp" : {
            "host"       : "192.168.50.48",
            "port"       : "9999",
            "level"      : "INFO",
            "formatter"  : "logstash",
            "processors" : ["web"]
        }
    },
    "logger" : {
        "integration" : {
            "handler" : ["file", "udp"],
            "level" : "INFO"
        },
        "caller" : {
            "handler" : ["file", "udp"],
            "level" : "INFO"
        },
        "detector" : {
            "handler" : ["file", "udp"],
            "level" : "INFO"
        },
        "controller" : {
            "handler" : ["file", "udp"],
            "level" : "INFO"
        }
    }
}
```
