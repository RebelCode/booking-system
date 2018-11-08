<?php

namespace RebelCode\Bookings\UnitTest;

use Dhii\Collection\MapInterface;
use Dhii\Data\Container\Exception\ContainerException;
use Dhii\Data\Container\Exception\NotFoundException;
use Dhii\Exception\InternalException;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Booking as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class BookingTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Bookings\Booking';

    /**
     * Creates a mock map instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|MapInterface
     */
    public function createMap()
    {
        return $this->getMockBuilder('Dhii\Collection\MapInterface')
                    ->setMethods(['get', 'has'])
                    ->getMockForAbstractClass();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = new TestSubject($this->createMap());

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'RebelCode\Bookings\BookingInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to assert whether the state map is correctly set.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $map     = $this->createMap();
        $subject = new TestSubject($map);

        $this->assertSame($map, $subject->getState(), 'Set and retrieved state maps are not the same');
    }

    /**
     * Tests the internal method that reads from state to assert that data is correctly read from the map.
     *
     * @since [*next-version*]
     */
    public function testGetFromState()
    {
        $key     = uniqid('key-');
        $value   = uniqid('value-');
        $default = uniqid('default-');

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $subject = new TestSubject($map);
        $reflect = $this->reflect($subject);

        $expected = $value;
        $actual   = $reflect->_getFromState($key, $default);

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the internal method that reads from state to assert that the default value is returned when a key is not
     * found in the map.
     *
     * @since [*next-version*]
     */
    public function testGetFromStateDefault()
    {
        $key     = uniqid('key-');
        $default = uniqid('default-');

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new NotFoundException());

        $subject = new TestSubject($map);
        $reflect = $this->reflect($subject);

        $expected = $default;
        $actual   = $reflect->_getFromState($key, $default);

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the internal method that reads from state to assert whether an internal exception is thrown when an error
     * occurs while reading data from the map.
     *
     * @since [*next-version*]
     */
    public function testGetFromStateInternalException()
    {
        $key     = uniqid('key-');
        $default = uniqid('default-');

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new ContainerException());

        $subject = new TestSubject($map);
        $reflect = $this->reflect($subject);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $reflect->_getFromState($key, $default);
    }

    /**
     * Tests the ID getter method to assert whether the ID is correctly retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetId()
    {
        $key   = TestSubject::K_STATE_ID;
        $value = rand(0, 100);

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $subject = new TestSubject($map);

        $expected = $value;
        $actual   = $subject->getId();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the ID getter method to assert whether the default value is returned when an ID does not exist in the
     * state map.
     *
     * @since [*next-version*]
     */
    public function testGetIdDefault()
    {
        $key = TestSubject::K_STATE_ID;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new NotFoundException());

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getId();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the ID getter method to assert whether an internal exception is thrown when an error occurs while reading
     * from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetIdInternalException()
    {
        $key = TestSubject::K_STATE_ID;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new ContainerException());

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getId();
    }

    /**
     * Tests the start time getter method to assert whether the ID is correctly retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetStart()
    {
        $key   = TestSubject::K_STATE_START;
        $value = rand(0, time());

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $subject = new TestSubject($map);

        $expected = $value;
        $actual   = $subject->getStart();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the start time getter method to assert whether the default value is returned when an ID does not exist in
     * the state map.
     *
     * @since [*next-version*]
     */
    public function testGetStartDefault()
    {
        $key = TestSubject::K_STATE_START;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new NotFoundException());

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getStart();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the start time getter method to assert whether an internal exception is thrown when an error occurs while
     * reading from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetStartInternalException()
    {
        $key = TestSubject::K_STATE_START;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new ContainerException());

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getStart();
    }

    /**
     * Tests the end time getter method to assert whether the ID is correctly retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetEnd()
    {
        $key   = TestSubject::K_STATE_END;
        $value = rand(0, time());

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $subject = new TestSubject($map);

        $expected = $value;
        $actual   = $subject->getEnd();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the end time getter method to assert whether the default value is returned when an ID does not exist in
     * the state map.
     *
     * @since [*next-version*]
     */
    public function testGetEndDefault()
    {
        $key = TestSubject::K_STATE_END;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new NotFoundException());

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getEnd();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the end time getter method to assert whether an internal exception is thrown when an error occurs while
     * reading from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetEndInternalException()
    {
        $key = TestSubject::K_STATE_END;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new ContainerException());

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getEnd();
    }

    /**
     * Tests the resource ID getter method to assert whether the ID is correctly retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetResourceIds()
    {
        $key   = TestSubject::K_STATE_RESOURCE_IDS;
        $value = [rand(0, 100), uniqid('id-')];

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $subject = new TestSubject($map);

        $expected = $value;
        $actual   = $subject->getResourceIds();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the resource ID getter method to assert whether the default value is returned when an ID does not exist in
     * the state map.
     *
     * @since [*next-version*]
     */
    public function testGetResourceIdsDefault()
    {
        $key = TestSubject::K_STATE_RESOURCE_IDS;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new NotFoundException());

        $subject = new TestSubject($map);

        $expected = [];
        $actual   = $subject->getResourceIds();

        $this->assertEquals($expected, $actual, 'Expected and retrieved value from state are not equal.');
    }

    /**
     * Tests the resource ID getter method to assert whether an internal exception is thrown when an error occurs while
     * reading from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetResourceIdsInternalException()
    {
        $key = TestSubject::K_STATE_RESOURCE_IDS;

        $map = $this->createMap();
        $map->expects($this->once())
            ->method('get')
            ->with($key)
            ->willThrowException(new ContainerException());

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getResourceIds();
    }

    /**
     * Tests the duration getter method to assert whether the duration is correctly calculated from the start and end
     * times in the state map.
     *
     * @since [*next-version*]
     */
    public function testGetDuration()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $start = rand(0, 100);
        $end   = rand(0, 100);

        $map->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnOnConsecutiveCalls($start, $end);

        $subject = new TestSubject($map);

        $expected = $end - $start;
        $actual   = $subject->getDuration();

        $this->assertEquals($expected, $actual, 'Expected and retrieved durations are not equal.');
    }

    /**
     * Tests the duration getter method to assert whether null is returned when the start time is null.
     *
     * @since [*next-version*]
     */
    public function testGetDurationStartNull()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $end = rand(0, 100);

        $map->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnOnConsecutiveCalls(null, $end);

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getDuration();

        $this->assertEquals($expected, $actual, 'Expected and retrieved durations are not equal.');
    }

    /**
     * Tests the duration getter method to assert whether null is returned when the end time is null.
     *
     * @since [*next-version*]
     */
    public function testGetDurationEndNull()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $start = rand(0, 100);

        $map->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnOnConsecutiveCalls($start, null);

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getDuration();

        $this->assertEquals($expected, $actual, 'Expected and retrieved durations are not equal.');
    }

    /**
     * Tests the duration getter method to assert whether null is returned when both the start and end times are null.
     *
     * @since [*next-version*]
     */
    public function testGetDurationStartEndNull()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $map->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnOnConsecutiveCalls(null, null);

        $subject = new TestSubject($map);

        $expected = null;
        $actual   = $subject->getDuration();

        $this->assertEquals($expected, $actual, 'Expected and retrieved durations are not equal.');
    }

    /**
     * Tests the duration getter method to assert whether an internal exception is thrown when an error occurs while
     * reading the start time from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetDurationStartInternalException()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $end = rand(0, 100);

        $map->expects($this->atLeastOnce())
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnCallback(function ($key) use ($startKey, $endKey, $end) {
                if ($key === $endKey) {
                    return $end;
                }

                throw new InternalException(null, null, new Exception());
            });

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getDuration();
    }

    /**
     * Tests the duration getter method to assert whether an internal exception is thrown when an error occurs while
     * reading the end time from the state map.
     *
     * @since [*next-version*]
     */
    public function testGetDurationEndInternalException()
    {
        $map = $this->createMap();

        $startKey = TestSubject::K_STATE_START;
        $endKey   = TestSubject::K_STATE_END;

        $start = rand(0, 100);

        $map->expects($this->atLeastOnce())
            ->method('get')
            ->withConsecutive([$startKey], [$endKey])
            ->willReturnCallback(function ($key) use ($startKey, $endKey, $start) {
                if ($key === $startKey) {
                    return $start;
                }

                throw new InternalException(null, null, new Exception());
            });

        $subject = new TestSubject($map);

        $this->setExpectedException('Dhii\Exception\InternalException');

        $subject->getDuration();
    }
}
