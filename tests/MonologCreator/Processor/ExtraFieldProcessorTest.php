<?php
namespace MonologCreator\Processor;

/**
 * Class ExtraFieldProcessorTestTest
 *
 * @package MonologCreator\Processor
 * @author Sebastian Götze <s.goetze@bigpoint.net>
 */
class ExtraFieldProcessorTestTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $fields = array(
            'test_field' => 'test'
        );
        $subject = new ExtraFieldProcessor($fields);
        $record  = array('extra' => array());
        $actual  = $subject->__invoke($record);
        $this->assertTrue(\array_key_exists('test_field', $actual['extra']));
        $this->assertTrue($actual['extra']['test_field'] === 'test');
    }
}
