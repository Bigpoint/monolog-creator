<?php

namespace MonologCreator\Factory;

use Monolog;

/**
 * Class HandlerTest
 *
 * @package MonologCreator\Factory
 */
class HandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormatterFactory = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormatter = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUdpSocket = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPredisClient = null;

    public function setUp()
    {
        parent::setUp();

        $this->mockFormatterFactory = $this->getMockBuilder('\MonologCreator\Factory\Formatter')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->mockFormatter = $this->getMockBuilder('\Monolog\Formatter\FormatterInterface')
            ->disableOriginalConstructor()
            ->setMethods(['format', 'formatBatch'])
            ->getMock();

        $this->mockUdpSocket = $this->getMockBuilder('\Monolog\Handler\SyslogUdp\UdpSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockPredisClient = $this->getMockBuilder('\Predis\Client')
        ->disableOriginalConstructor()
        ->getMock();
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no handler configuration found
     */
    public function testCreateFailNoConfig()
    {
        $factory = new Handler(array(), array(), $this->mockFormatterFactory);
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
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
            $this->mockFormatterFactory
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
    public function testCreateUdpFailNoHost()
    {
        $config = json_decode(
            '{
                "handler" : {
                    "udp" : {}
                }
            }',
            true
        );

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
        $factory->create('udp', 'INFO');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage port configuration for udp handler is missing
     */
    public function testCreateUdpFailNoPort()
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
        $factory->create('udp', 'INFO');
    }

    public function testCreateUdp()
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

        $factory = $this->getMockBuilder('\MonologCreator\Factory\Handler')
            ->setConstructorArgs(
                [
                    $config,
                    array(
                        'INFO' => Monolog\Logger::INFO,
                    ),
                    $this->mockFormatterFactory,
                ]
            )
            ->setMethods(['createUdpSocket'])
            ->getMock();

        $factory->expects($this->exactly(1))
            ->method('createUdpSocket')
            ->with(
                $this->equalTo('192.168.50.48'),
                $this->equalTo('9999')
            )
            ->will($this->returnValue($this->mockUdpSocket));

        $handler = $factory->create('udp', 'INFO');

        $this->assertInstanceOf(
            '\MonologCreator\Handler\Udp',
            $handler
        );
    }

    public function testCreateWithFormatter()
    {
        $this->mockFormatterFactory
            ->expects($this->exactly(1))
            ->method('create')
            ->with($this->equalTo('logstash'))
            ->will($this->returnValue($this->mockFormatter));

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
            $this->mockFormatterFactory
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
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

        $factory = new Handler($config, array(), $this->mockFormatterFactory);
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

        $factory = $this->getMockBuilder('\MonologCreator\Factory\Handler')
            ->setConstructorArgs(
                [
                    $config,
                    array(
                        'INFO' => Monolog\Logger::INFO,
                    ),
                    $this->mockFormatterFactory,
                ]
            )
            ->setMethods(['createPredisClient'])
            ->getMock();

        $factory->expects($this->exactly(1))
            ->method('createPredisClient')
            ->with($this->equalTo('mockUrl'))
            ->will($this->returnValue($this->mockPredisClient));

        $factory->create('redis', 'INFO');
    }
}
