<?php

namespace MonologCreator\Factory;

/**
 * Class FormatterTest
 *
 * @package MonologCreator\Factory
 */
class FormatterTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateFailNoConfig()
    {
        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('no formatter configuration found');

        $factory = new Formatter(array());
        $factory->create('mockFormatter');
    }

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

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('no formatter configuration found for formatterType: mockFormatter');

        $factory = new Formatter($config);
        $factory->create('mockFormatter');
    }

    public function testCreateFailNotSupportedFormatter()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "mockFormatter" : {
                        "type" : "test"
                    }
                }
            }',
            true
        );

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('formatter type: mockFormatter is not supported');

        $factory = new Formatter($config);
        $factory->create('mockFormatter');
    }

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

        $this->expectException(\MonologCreator\Exception::class);
        $this->expectExceptionMessage('type configuration for logstash formatter is missing');

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

    public function testCreateLogstash()
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

    public function testCreateJson()
    {
        $config = json_decode(
            '{
                "formatter" : {
                    "json" : {}
                }
            }',
            true
        );

        $factory = new Formatter($config);
        $actual = $factory->create('json');

        $this->assertInstanceOf(
            '\Monolog\Formatter\JsonFormatter',
            $actual
        );
    }
}
