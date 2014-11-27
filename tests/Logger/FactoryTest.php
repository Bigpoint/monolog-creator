<?php

namespace Logger;

/**
 *
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateDefaultLogger()
    {
        $configString = '{
            "handler" : {
                "stream" : {
                    "path"      : "./app.log"
                }
            },
            "logger" : {
                "_default" : {
                    "handler" : ["stream"],
                    "level" : "WARNING"
                }
            }}'
        ;

        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger instanceof \Monolog\Logger);
        $this->assertEquals($loggerName, $testLogger->getName());

        // check handler
        $handlers = $testLogger->getHandlers();
        $this->assertEquals(1, count($handlers));
        $this->assertTrue($handlers[0] instanceof \Monolog\Handler\StreamHandler);
    }

    /**
     *
     * @param string $configString
     *
     * @expectedException Logger\Exception
     * @dataProvider dataProviderCreateDefaultLoggerFail
     */
    public function testCreateDefaultLoggerFail($configString)
    {
        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);
    }

    public function dataProviderCreateDefaultLoggerFail()
    {
        return array(
            // missing default logger
            array(
                '{
                    "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                }}'
            ),
            array(
                '{
                    "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                    "_default" : {
                    }
                }}'
            ),
        );
    }

    public function testCreateDefaultLoggerWithFormatter()
    {
        $configString = '{
            "handler" : {
                "stream" : {
                    "path"      : "./app.log",
                    "formatter" : "logstash"
                }
            },
            "formatter" : {
                "logstash" : {
                    "type" : "test"
                }
            },
            "logger" : {
                "_default" : {
                    "handler" : ["stream"],
                    "level" : "WARNING"
                }
            }}'
        ;

        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger instanceof \Monolog\Logger);
        $this->assertEquals($loggerName, $testLogger->getName());

        // check handler
        $handlers = $testLogger->getHandlers();
        $this->assertEquals(1, count($handlers));
        $this->assertTrue($handlers[0] instanceof \Monolog\Handler\StreamHandler);

        // check formatter
        $this->assertTrue(
            $handlers[0]->getFormatter() instanceof \Monolog\Formatter\LogstashFormatter
        );
    }

        /**
     *
     * @param string $configString
     *
     * @expectedException Logger\Exception
     * @dataProvider dataProviderCreateDefaultLoggerFailWithFormatter
     */
    public function testCreateDefaultLoggerFailWithFormatter($configString)
    {
        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);
    }

    public function dataProviderCreateDefaultLoggerFailWithFormatter()
    {
        return array(
            // missing formatter
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path"      : "./app.log",
                        "formatter" : "logstash"
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // missing formatter config
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path"      : "./app.log",
                        "formatter" : "logstash"
                    }
                },
                "formatter" : {
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // missing formatter config key for logstash
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path"      : "./app.log",
                        "formatter" : "logstash"
                    }
                },
                "formatter" : {
                    "logstash" : {
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // not supported formatter
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path"      : "./app.log",
                        "formatter" : "fubar"
                    }
                },
                "formatter" : {
                    "logstash" : {
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
        );
    }


    public function testCreateStreamLogger()
    {
        $configString = '{
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
                    "level" : "INFO"
                }
            }}'
        ;

        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger instanceof \Monolog\Logger);
        $this->assertEquals($loggerName, $testLogger->getName());

        // check handler
        $handlers = $testLogger->getHandlers();
        $this->assertEquals(1, count($handlers));
        $this->assertTrue($handlers[0] instanceof \Monolog\Handler\StreamHandler);
    }

    /**
     *
     * @param string $configString
     *
     * @expectedException \Logger\Exception
     * @dataProvider dataProviderCreateStreamLoggerFail
     */
    public function testCreateStreamLoggerFail($configString)
    {
        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger = $loggerFactory->createLogger($loggerName);
    }

    public function dataProviderCreateStreamLoggerFail()
    {
        return array(
            // missing handler key
            array(
                '{
                "logger" : {
                    "test" : {
                        "handler" : ["stream"],
                        "level" : "INFO"
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // missing handler config
            array(
                '{
                "handler" : {
                },
                "logger" : {
                    "test" : {
                        "handler" : ["stream"],
                        "level" : "INFO"
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // not supported handler
            array(
                '{
                "handler" : {
                    "fubar" : {
                    }
                },
                "logger" : {
                    "test" : {
                        "handler" : ["fubar"],
                        "level" : "INFO"
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // missing stream handler path config
            array(
                '{
                "handler" : {
                    "stream" : {
                    }
                },
                "logger" : {
                    "test" : {
                        "handler" : ["stream"],
                        "level" : "INFO"
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // missing logger key
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                }}'
            ),
            // empty logger
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                    "test" : {},
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // empty level
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                    "test" : {
                        "handler" : ["stream"]
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
            // wrong level
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                    "test" : {
                        "handler" : ["stream"],
                        "level" : "info"
                    },
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    }
                }}'
            ),
        );
    }

    public function testCreateStreamLoggerMultipleInstances()
    {
        $configString = '{
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
                    "level" : "INFO"
                }
            }}'
        ;

        $config = json_decode($configString, true);
        $loggerName = 'test';

        $loggerFactory = new Factory($config);
        $testLogger1 = $loggerFactory->createLogger($loggerName);
        $testLogger2 = $loggerFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger1 instanceof \Monolog\Logger);
        $this->assertTrue($testLogger2 instanceof \Monolog\Logger);
        $this->assertTrue($testLogger1 === $testLogger2);
    }

    public function testCreateUdpLogger()
    {
        $configString = '{
            "handler" : {
                "udp" : {
                    "host"      : "192.168.50.48",
                    "port"      : 9999,
                    "level"     : "INFO",
                    "formatter" : "logstash"
                }
            },
            "formatter" : {
                "logstash" : {
                    "type" : "test"
                }
            },
            "logger" : {
                "_default" : {
                    "handler" : ["udp"],
                    "level" : "WARNING"
                },
                "test" : {
                    "handler" : ["udp"],
                    "level" : "INFO"
                }
            }}'
        ;

        $config = json_decode($configString, true);
        $loggerName = 'test';

        // mock factory and udp socket
        $mockFactory = $this->getMock(
            '\Logger\Factory',
            array('_createUdpSocket'),
            array($config)
        );

        $mockSocket = $this->getMock(
            '\Monolog\Handler\SyslogUdp\UdpSocket',
            array(),
            array(),
            '',
            false
        );

        $mockFactory->expects($this->exactly(1))
            ->method('_createUdpSocket')
            ->with(
                $this->equalTo('192.168.50.48'),
                $this->equalTo(9999)
            )
            ->will($this->returnValue($mockSocket));

        $testLogger = $mockFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger instanceof \Monolog\Logger);
        $this->assertEquals($loggerName, $testLogger->getName());

        // check handler
        $handlers = $testLogger->getHandlers();
        $this->assertEquals(1, count($handlers));
        $this->assertTrue($handlers[0] instanceof \Logger\Handler\Udp);
    }


    /**
     *
     * @param string $configString
     *
     * @expectedException Logger\Exception
     * @dataProvider dataProviderCreateUdpLoggerFail
     */
    public function testCreateUdpLoggerFail($configString)
    {
        $config = json_decode($configString, true);
        $loggerName = 'test';

        $factory = new Factory($config);
        $testLogger = $factory->createLogger($loggerName);
    }

    public function dataProviderCreateUdpLoggerFail()
    {
        return array(
            // missing host key
            array(
                '{
                "handler" : {
                    "udp" : {
                        "port"      : 9999,
                        "level"     : "INFO",
                        "formatter" : "logstash"
                    }
                },
                "formatter" : {
                    "logstash" : {
                        "type" : "test"
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["udp"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["udp"],
                        "level" : "INFO"
                    }
                }}'
            ),
            // missing port key
            array(
                '{
                "handler" : {
                    "udp" : {
                        "host"      : "127.0.0.1",
                        "level"     : "INFO",
                        "formatter" : "logstash"
                    }
                },
                "formatter" : {
                    "logstash" : {
                        "type" : "test"
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["udp"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["udp"],
                        "level" : "INFO"
                    }
                }}'
            )
        );
    }

    /**
     * @expectedException \Logger\Exception
     * @expectedExceptionMessage formatter type: mockFomatter is not supported
     */
    public function testCreateFormatterFail()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "udp" : {
                        "host"      : "192.168.50.48",
                        "port"      : 9999,
                        "level"     : "INFO",
                        "formatter" : "mockFomatter"
                    }
                },
                "formatter" : {
                    "mockFomatter" : {
                        "type" : "test"
                    }
                },
                "logger" : {
                    "_default" : {
                        "handler" : ["udp"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["udp"],
                        "level" : "INFO"
                    }
                }
            }',
            true
        );
        $loggerName = 'test';

        $factory = new Factory($config);
        $factory->createLogger($loggerName);
    }
}
