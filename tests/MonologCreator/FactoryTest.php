<?php
namespace MonologCreator;

/**
 * Class FactoryTest
 *
 * @package MonologCreator
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockFormatterFactory = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockHandlerFactory = null;

    public function setUp()
    {
        parent::setUp();

        $this->_mockFormatterFactory = $this->getMock(
            '\MonologCreator\Factory\Formatter',
            array(),
            array(),
            '',
            false
        );
        $this->_mockHandlerFactory = $this->getMock(
            '\MonologCreator\Factory\Handler',
            array(
                'create',
            ),
            array(),
            '',
            false
        );
    }

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
     *
     * @param string $configString
     *
     * @expectedException \MonologCreator\Exception
     * @dataProvider dataProviderCreateDefaultLoggerFail
     */
    public function testCreateDefaultLoggerFail($configString)
    {
        $config = json_decode($configString, true);

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
                        "processors": ["web", "requestId"],
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
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage processor type: mockProccessor is not supported
     */
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

        $factory = new Factory($config);
        $factory->createLogger($loggerName);
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no logger configuration found
     */
    public function testCreateLoggerNoConfig()
    {
        $factory = new Factory(array());
        $factory->createLogger('test');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no level configurated for logger: test
     */
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

        $factory = new Factory($config);
        $factory->createLogger('test');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage invalid level: mockLevel
     */
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

        $factory = new Factory($config);
        $factory->createLogger('test');
    }
}
