<?php

namespace RebelCode\Bookings\Util\Time\UnitTest;

use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Util\Time\Period as TestSubject;
use ReflectionClass;
use ReflectionException;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class PeriodTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Bookings\Util\Time\Period';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return TestSubject|MockObject
     */
    public function createInstance()
    {
        return $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                    ->setConstructorArgs(func_get_args())
                    ->setMethods(['_normalizeTimestamp'])
                    ->getMock();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = new TestSubject(0, 0);

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\Time\PeriodInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to assert whether the start and end values are correctly normalized and stored.
     *
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        /* @var $subject MockObject|TestSubject */
        $subject = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(['_normalizeTimestamp'])
                        ->disableOriginalConstructor()
                        ->getMock();

        $start  = rand(1, 100);
        $nStart = rand(1, 100);
        $end    = rand(100, 200);
        $nEnd   = rand(100, 200);

        $subject->expects($this->exactly(2))
                ->method('_normalizeTimestamp')
                ->withConsecutive([$start], [$end])
                ->willReturnOnConsecutiveCalls($nStart, $nEnd);

        $reflect     = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();
        $constructor->invoke($subject, $start, $end);

        $this->assertEquals($nStart, $subject->getStart());
        $this->assertEquals($nEnd, $subject->getEnd());
    }

    /**
     * Tests the duration getter method to assert whether it correctly calculates the duration using the stored start
     * and end times.
     *
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testGetDuration()
    {
        /* @var $subject MockObject|TestSubject */
        $subject = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(['_normalizeTimestamp'])
                        ->disableOriginalConstructor()
                        ->getMock();

        $start    = rand(1, 100);
        $nStart   = rand(1, 100);
        $end      = rand(100, 200);
        $nEnd     = rand(100, 200);
        $expected = $nEnd - $nStart;

        $subject->expects($this->exactly(2))
                ->method('_normalizeTimestamp')
                ->withConsecutive([$start], [$end])
                ->willReturnOnConsecutiveCalls($nStart, $nEnd);

        $reflect     = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();
        $constructor->invoke($subject, $start, $end);

        $this->assertEquals($expected, $subject->getDuration());
    }

    /**
     * Tests the constructor with an invalid start to assert whether an invalid argument exception is thrown.
     *
     * The timestamp normalization functionality is tested separately. This test mocks that functionality to test the
     * behavior of the subject when normalization fails.
     *
     * @since [*next-version*]
     *
     * @throws ReflectionException
     */
    public function testConstructorInvalidStart()
    {
        /* @var $subject MockObject|TestSubject */
        $subject = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(['_normalizeTimestamp'])
                        ->disableOriginalConstructor()
                        ->getMock();

        $start = rand(1, 100);
        $end   = rand(100, 200);

        $subject->expects($this->once())
                ->method('_normalizeTimestamp')
                ->with($start)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect     = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();
        $constructor->invoke($subject, $start, $end);
    }

    /**
     * Tests the constructor with an invalid end to assert whether an invalid argument exception is thrown.
     *
     * The timestamp normalization functionality is tested separately. This test mocks that functionality to test the
     * behavior of the subject when normalization fails.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testConstructorInvalidEnd()
    {
        /* @var $subject MockObject|TestSubject */
        $subject = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(['_normalizeTimestamp'])
                        ->disableOriginalConstructor()
                        ->getMock();

        $start = rand(1, 100);
        $end   = rand(100, 200);

        $subject->expects($this->exactly(2))
                ->method('_normalizeTimestamp')
                ->withConsecutive([$start], [$end])
            // Throws when the arg is $end
                ->willReturnCallback(function ($arg) use ($end) {
                if ($arg === $end) {
                    throw new InvalidArgumentException();
                }

                return $arg;
            });

        $this->setExpectedException('InvalidArgumentException');

        $reflect     = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();
        $constructor->invoke($subject, $start, $end);
    }
}
