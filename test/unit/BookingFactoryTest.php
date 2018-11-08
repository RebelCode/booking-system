<?php

namespace RebelCode\Bookings\UnitTest;

use Dhii\Collection\MapFactoryInterface;
use Dhii\Collection\MapInterface;
use Dhii\Data\StateAwareFactoryInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\BookingFactory as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class BookingFactoryTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Bookings\BookingFactory';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return MapFactoryInterface|MockObject
     */
    public function createMapFactory()
    {
        $mock = $this->getMockBuilder('Dhii\Collection\MapFactoryInterface')
                     ->setMethods(['make'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates a new mock map instance.
     *
     * @since [*next-version*]
     *
     * @return MapInterface|MockObject
     */
    public function createMap()
    {
        $mock = $this->getMockBuilder('Dhii\Collection\MapInterface')
                     ->setMethods(['get', 'hash'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = new TestSubject($this->createMapFactory());

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\Data\StateAwareFactoryInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the make method to assert whether a new instance can be created using a typical array as config.
     *
     * @since [*next-version*]
     */
    public function testMake()
    {
        $data = [uniqid('some-') => uniqid('data-')];
        $map  = $this->createMap();

        $mapFactory = $this->createMapFactory();
        $mapFactory->expects($this->once())
                   ->method('make')
                   ->with([MapFactoryInterface::K_DATA => $data])
                   ->willReturn($map);

        $subject = new TestSubject($mapFactory);

        $result = $subject->make([StateAwareFactoryInterface::K_DATA => $data]);
        $actual = $result->getState();

        $this->assertSame($map, $actual, 'Created subject has wrong state map.');
    }

    /**
     * Tests the make method to assert whether a new instance can be created using a map as the data in the config.
     *
     * @since [*next-version*]
     */
    public function testMakeMap()
    {
        $map = $this->createMap();

        $mapFactory = $this->createMapFactory();
        $mapFactory->expects($this->never())
                   ->method('make');

        $subject = new TestSubject($mapFactory);

        $result = $subject->make([StateAwareFactoryInterface::K_DATA => $map]);
        $actual = $result->getState();

        $this->assertSame($map, $actual, 'Created subject has wrong state map.');
    }

    /**
     * Tests the make method to assert whether a new instance can be created without any config.
     *
     * @since [*next-version*]
     */
    public function testMakeNoConfig()
    {
        $map = $this->createMap();

        $mapFactory = $this->createMapFactory();
        $mapFactory->expects($this->once())
                   ->method('make')
                   ->with([MapFactoryInterface::K_DATA => []])
                   ->willReturn($map);

        $subject = new TestSubject($mapFactory);

        $result = $subject->make();
        $actual = $result->getState();

        $this->assertSame($map, $actual, 'Created subject has wrong state map.');
    }
}
