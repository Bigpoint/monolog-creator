<?php
namespace MonologCreator\Processor;

/**
 * Class FormatterTest
 *
 * @package MonologCreator\Factory
 */
class RequestIdTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\MonologCreator\Processor\RequestId
     */
    private $subject = null;

    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_generateUUID',
                    )
                )
                ->getMock();
        $this->subject->expects($this->once())
            ->method('_generateUUID');
        $this->subject->__construct();
    }

    public function testConstructorWithUUID()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_generateUUID',
                    )
                )
                ->getMock();
        $this->subject->expects($this->never())
            ->method('_generateUUID');
        $this->subject->__construct('mock');
    }

    public function testInvoke()
    {
        $this->subject  = new RequestId('mock');
        $record         = array('extra' => array());
        $expectedRecord = array('extra' => array('request_id' => 'mock'));
        $actual         = $this->subject->__invoke($record);
        $this->assertEquals($expectedRecord, $actual);
    }

    public function testgenerateUUIDWithRandomBytes()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_isCallable',
                        '_randomBytes',
                        '_generateUUIDFromData',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->subject->expects($this->at(0))
            ->method('_isCallable')
            ->with($this->equalTo('random_bytes'))
            ->willReturn(true);

        $this->subject->expects($this->at(1))
            ->method('_randomBytes')
            ->with($this->equalTo(16))
            ->willReturn('abcdefgh12345678');

        $this->subject->expects($this->once())
            ->method('_generateUUIDFromData')
            ->with($this->equalTo('abcdefgh12345678'));

        $this->subject->__construct(null);
    }

    public function testgenerateUUIDWithOpenSSLRandomPseudoBytes()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_isCallable',
                        '_opensslRandomPseudoBytes',
                        '_generateUUIDFromData',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->subject->expects($this->at(0))
            ->method('_isCallable')
            ->with($this->equalTo('random_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(1))
            ->method('_isCallable')
            ->with($this->equalTo('openssl_random_pseudo_bytes'))
            ->willReturn(true);

        $this->subject->expects($this->at(2))
            ->method('_opensslRandomPseudoBytes')
            ->with($this->equalTo(16))
            ->willReturn('abcdefgh12345678');

        $this->subject->expects($this->once())
            ->method('_generateUUIDFromData')
            ->with($this->equalTo('abcdefgh12345678'));

        $this->subject->__construct(null);
    }

    public function testgenerateUUIDWithMtRand()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_isCallable',
                        '_generateBytesWithMtRand',
                        '_generateUUIDFromData',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->subject->expects($this->at(0))
            ->method('_isCallable')
            ->with($this->equalTo('random_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(1))
            ->method('_isCallable')
            ->with($this->equalTo('openssl_random_pseudo_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(2))
            ->method('_isCallable')
            ->with($this->equalTo('mt_rand'))
            ->willReturn(true);

        $this->subject->expects($this->at(3))
            ->method('_generateBytesWithMtRand')
            ->with($this->equalTo(16))
            ->willReturn('abcdefgh12345678');

        $this->subject->expects($this->once())
            ->method('_generateUUIDFromData')
            ->with($this->equalTo('abcdefgh12345678'));

        $this->subject->__construct(null);
    }

    public function testgenerateUUIDWithoutRNG()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_isCallable',
                        '_generateBytesWithMtRand',
                        '_generateUUIDFromData',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->subject->expects($this->at(0))
            ->method('_isCallable')
            ->with($this->equalTo('random_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(1))
            ->method('_isCallable')
            ->with($this->equalTo('openssl_random_pseudo_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(2))
            ->method('_isCallable')
            ->with($this->equalTo('mt_rand'))
            ->willReturn(false);

        $this->subject->expects($this->never())
            ->method('_generateUUIDFromData');

        $this->subject->__construct(null);
    }

    public function testgenerateBytesWithMtRand()
    {
        $this->subject =
            $this->getMockBuilder('MonologCreator\Processor\RequestId')
                ->setMethods(
                    array(
                        '_isCallable',
                        '_generateUUIDFromData',
                        '_mtRand',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->subject->expects($this->at(0))
            ->method('_isCallable')
            ->with($this->equalTo('random_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(1))
            ->method('_isCallable')
            ->with($this->equalTo('openssl_random_pseudo_bytes'))
            ->willReturn(false);
        $this->subject->expects($this->at(2))
            ->method('_isCallable')
            ->with($this->equalTo('mt_rand'))
            ->willReturn(true);

        $this->subject->expects($this->exactly(16))
            ->method('_mtRand')
            ->with($this->equalTo(0), $this->equalTo(255))
            ->willReturn(97);

        $this->subject->expects($this->once())
            ->method('_generateUUIDFromData')
            ->with($this->equalTo('aaaaaaaaaaaaaaaa'));

        $this->subject->__construct(null);
    }
}
