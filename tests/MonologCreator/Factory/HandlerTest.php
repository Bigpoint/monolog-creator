<?php
namespace MonologCreator\Factory;

use \Monolog;

/**
 * Class HandlerTest
 *
 * @package MonologCreator\Factory
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockFormatterFactory = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockFormatter = null;

    public function setUp()
    {
        parent::setUp();

        $this->_mockFormatterFactory = $this->getMock(
            '\MonologCreator\Factory\Formatter',
            array(
                'create',
            ),
            array(),
            '',
            false
        );
        $this->_mockFormatter = $this->getMock(
            '\Monolog\Formatter\FormatterInterface',
            array(
                'format',
                'formatBatch',
            ),
            array(),
            '',
            false
        );
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no handler configuration found
     */
    public function testCreateFailNoConfig()
    {
        $factory = new Handler(array(), array(), $this->_mockFormatterFactory);
        $factory->create('mockHandler', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no handler configuration found for handlerType: mockHandler
     */
    public function testCreateFailWrongHandlerType()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('mockHandler', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage handler type: mockHandler is not supported
     */
    public function testCreateFailNotSupported()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "mockHandler" : {
                        "path" : "./app.log"
                    }
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('mockHandler', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage path configuration for stream handler is missing
     */
    public function testCreateStreamHandlerFail()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "stream" : {}
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('stream', 'INFO');
    }

    public function testCreateStreamHandler()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "stream" : {
                        "path" : "./app.log"
                    }
                }
            }',
            true
        );

        $factory = new Handler(
            $config,
            array(
                'INFO' => Monolog\Logger::INFO,
            ),
            $this->_mockFormatterFactory
        );
        $handler = $factory->create('stream', 'INFO');

        $this->assertInstanceOf(
            '\Monolog\Handler\StreamHandler',
            $handler
        );
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage host configuration for udp handler is missing
     */
    public function testCreateUdbFailNoHost()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "udp" : {}
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('udp', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage port configuration for udp handler is missing
     */
    public function testCreateUdbFailNoPort()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "udp" : {
                        "host" : "192.168.50.48"
                    }
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('udp', 'INFO');
    }

    public function testCreateUdb()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "udp" : {
                        "host"      : "192.168.50.48",
                        "port"      : 9999,
                        "level"     : "INFO"
                    }
                }
            }',
            true
        );

        $factory = new Handler(
            $config,
            array(
                'INFO' => Monolog\Logger::INFO,
            ),
            $this->_mockFormatterFactory
        );
        $handler = $factory->create('udp', 'INFO');

        $this->assertInstanceOf(
            '\MonologCreator\Handler\Udp',
            $handler
        );
    }

    public function testCreateWithFormatter()
    {
        $this->_mockFormatterFactory
            ->expects($this->exactly(1))
            ->method('create')
            ->with($this->equalTo('logstash'))
            ->will($this->returnValue($this->_mockFormatter));

        $config = json_decode(
            '{
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
                }
            }',
            true
        );

        $factory = new Handler(
            $config,
            array(
                'INFO' => Monolog\Logger::INFO,
            ),
            $this->_mockFormatterFactory
        );
        $handler = $factory->create('stream', 'INFO');

        $this->assertInstanceOf(
            '\Monolog\Handler\StreamHandler',
            $handler
        );

        $this->assertInstanceOf(
            '\Monolog\Formatter\FormatterInterface',
            $handler->getFormatter()
        );
    }
}
