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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockUdpSocket = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockPredisClient = null;

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
        $this->_mockUdpSocket = $this->getMock(
            '\Monolog\Handler\SyslogUdp\UdpSocket',
            array(),
            array(),
            '',
            false
        );
        $this->_mockPredisClient = $this->getMock(
            '\Predis\Client',
            array(),
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

        $factory = $this->getMock(
            '\MonologCreator\Factory\Handler',
            array(
                '_createUdpSocket',
            ),
            array(
                $config,
                array(
                    'INFO' => Monolog\Logger::INFO,
                ),
                $this->_mockFormatterFactory,
            )
        );

        $factory->expects($this->exactly(1))
            ->method('_createUdpSocket')
            ->with(
                $this->equalTo('192.168.50.48'),
                $this->equalTo('9999')
            )
            ->will($this->returnValue($this->_mockUdpSocket));

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

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage url configuration for redis handler is missing
     */
    public function testCreateRedisFailNoUrl()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "redis" : {}
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('redis', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage key configuration for redis handler is missing
     */
    public function testCreateRedisFailNoKey()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "redis" : {
                        "url" : "mockUrl"
                    }
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->_mockFormatterFactory);
        $factory->create('redis', 'INFO');
    }

    public function testCreateRedis()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "redis" : {
                        "url" : "mockUrl",
                        "key" : "mockKey"
                    }
                }
            }',
            true
        );

        $factory = $this->getMock(
            '\MonologCreator\Factory\Handler',
            array(
                '_createPredisClient',
            ),
            array(
                $config,
                array(
                    'INFO' => Monolog\Logger::INFO,
                ),
                $this->_mockFormatterFactory,
            )
        );

        $factory->expects($this->exactly(1))
            ->method('_createPredisClient')
            ->with($this->equalTo('mockUrl'))
            ->will($this->returnValue($this->_mockPredisClient));

        $factory->create('redis', 'INFO');
    }
}
