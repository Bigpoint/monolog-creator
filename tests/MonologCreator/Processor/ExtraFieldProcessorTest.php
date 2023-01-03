<?php

namespace MonologCreator\Processor;

/**
 * Class ExtraFieldProcessorTestTest
 *
 * @package MonologCreator\Processor
 * @author Sebastian Götze <s.goetze@bigpoint.net>
 */
class ExtraFieldProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function testInvoke()
    {
        $fields = array(
            'test_field' => 'test'
        );

        $subject = new ExtraFieldProcessor($fields);
        $record  = new \Monolog\LogRecord(
            new \DateTimeImmutable(),
            'testChannel',
            \Monolog\Level::Debug,
            'testMessage',
        );
        $actual  = $subject->__invoke($record);

        $this->assertTrue(\array_key_exists('test_field', $actual['extra']));
        $this->assertTrue($actual['extra']['test_field'] === 'test');
    }
}
