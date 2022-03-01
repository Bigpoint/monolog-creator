<?php

namespace MonologCreator\Factory;

/**
 * Class FormatterTest
 *
 * @package MonologCreator\Factory
 */
class FormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \MonologCreator\Exception
     * @expectedExceptionMessage no formatter configuration found
     */
    public function testCreateFailNoConfig()
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

    public function testCreateLine()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {}
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
    }

    public function testCreateLineFormat()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {
                        "format" : "mockFormat"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
    }

    public function testCreateLineDateFormat()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {
                        "dateFormat" : "mockDateFormat"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
    }

    public function testCreateLineIncludeStacktraces()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {
                        "includeStacktraces" : "true"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
    }

    public function testCreateLineAllowInlineLineBreaks()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {
                        "allowInlineLineBreaks" : "true"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
    }

    public function testCreateLineIgnoreEmptyContextAndExtra()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "line" : {
                        "ignoreEmptyContextAndExtra" : "true"
                    }
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('line');

        $this->assertInstanceOf(
            '\Monolog\Formatter\LineFormatter',
            $actual
        );
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
