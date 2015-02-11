<?php
namespace MonologCreator\Factory;

/**
 * Class FormatterTest
 *
 * @package MonologCreator\Factory
 */
class FormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no formatter configuration found
     */
    public function testCreatFailNoConfig()
    {
        $factory = new Formatter(array());
        $factory->create('mockFomatter');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no formatter configuration found for formatterType: mockFomatter
     */
    public function testCreateFailNoConfigurationForFormatter()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "mockFormatter2" : {
                        "type" : "test"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $factory->create('mockFomatter');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage formatter type: mockFomatter is not supported
     */
    public function testCreateFailNotSupportedFormatter()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "mockFomatter" : {
                        "type" : "test"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $factory->create('mockFomatter');
    }

    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage type configuration for logstash foramtter is missing
     */
    public function testCreateLogstashFailNoTypeConfiguration()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "logstash" : {}
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $factory->create('logstash');
    }

    public function testCreate()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "logstash" : {
                        "type" : "test"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('logstash');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LogstashFormatter',
            $actual
        );
    }
}
