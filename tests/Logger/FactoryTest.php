<?php

namespace Logger;

/**
 *
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateStreamLogger()
    {
        $configString = '{
            "handler" : {
                "stream" : {
                    "path" : "./app.log"
                }
            },
            "logger" : {
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
     * @expectedException Logger\Exception
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
            // missing logger
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
            // empty logger
            array(
                '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                },
                "logger" : {
                    "test" : {}
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
                    }
                }}'
            ),
        );
    }
}
