<?php

namespace MonologCreator;

/**
 * Class FactoryTest
 *
 * @package MonologCreator
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
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
    }

    public function testCreateDefaultLoggerCached()
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

        $testLogger2 = $loggerFactory->createLogger($loggerName);

        // check object
        $this->assertTrue($testLogger2 instanceof \Monolog\Logger);
        $this->assertEquals($loggerName, $testLogger2->getName());

        // compare objects
        $this->assertTrue($testLogger === $testLogger2);
    }

    /**
     * @dataProvider dataProviderCreateDefaultLoggerFail
     */
    public function testCreateDefaultLoggerFail(string $configString)
    {
        $config = json_decode($configString, true);

        $this->expectException(\MonologCreator\Exception::class);

        $loggerFactory = new Factory($config);
        $loggerFactory->createLogger('test');
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

    public function testCreateLoggerWithProcessor()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "stream" : {
                        "path"      : "./fubar.log",
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
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["stream"],
                        "processors": ["web", "requestId", "extraFields"],
                        "level" : "INFO"
                    }
                }
            }',
            true
        );

        $factory    = new Factory($config);
        $logger     = $factory->createLogger('test');
        $processors = $logger->getProcessors();

        $this->assertInstanceOf(
            '\Monolog\Processor\WebProcessor',
            $processors[0]
        );
        $this->assertInstanceOf(
            '\MonologCreator\Processor\RequestId',
            $processors[1]
        );
        $this->assertInstanceOf(
            '\MonologCreator\Processor\ExtraFields',
            $processors[2]
        );
    }

    public function testCreateLoggerWithProcessorFail()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "stream" : {
                        "path"      : "./fubar.log",
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
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["stream"],
                        "processors": ["mockProccessor"],
                        "level" : "INFO"
                    }
                }
            }',
            true
        );
        $loggerName = 'test';

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('processor type: mockProccessor is not supported');

        $factory = new Factory($config);
        $factory->createLogger($loggerName);
    }

    public function testCreateLoggerNoConfig()
    {
        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('no logger configuration found');

        $factory = new Factory(array());
        $factory->createLogger('test');
    }

    public function testCreateLoggerNoLevel()
    {
        $config = json_decode(
            '{
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["stream"],
                        "processors": ["mockProccessor"]
                    }
                }
            }',
            true
        );

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('no level configured for logger: test');

        $factory = new Factory($config);
        $factory->createLogger('test');
    }

    public function testCreateLoggerInvalidLevel()
    {
        $config = json_decode(
            '{
                "logger" : {
                    "_default" : {
                        "handler" : ["stream"],
                        "level" : "WARNING"
                    },
                    "test" : {
                        "handler" : ["stream"],
                        "processors": ["mockProccessor"],
                        "level" : "mockLevel"
                    }
                }
            }',
            true
        );

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('invalid level: MOCKLEVEL');

        $factory = new Factory($config);
        $factory->createLogger('test');
    }
}
