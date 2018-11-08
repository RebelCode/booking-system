<?php

namespace RebelCode\Bookings\UnitTest\Transitioner;

use Dhii\Collection\MapInterface;
use Dhii\Data\Container\Exception\NotFoundException;
use Dhii\Data\Exception\CouldNotTransitionExceptionInterface;
use Dhii\Data\Exception\TransitionerExceptionInterface;
use Dhii\Data\StateAwareFactoryInterface;
use Dhii\Data\StateAwareInterface;
use Dhii\State\ReadableStateMachineInterface;
use Dhii\State\StateMachineFactoryInterface;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Transitioner\BookingTransitioner;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Bookings\Transitioner\BookingTransitioner}.
 *
 * @since [*next-version*]
 */
class BookingTransitionerTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Bookings\Transitioner\BookingTransitioner';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods
     *
     * @return MockObject
     */
    public function createInstance(array $methods = [])
    {
        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods($methods)
                        ->disableOriginalConstructor();

        return $builder->getMock();
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition       = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a mock map instance.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass $data The data for the map.
     *
     * @return MockObject|MapInterface The created map instance.
     */
    public function createMap($data)
    {
        $map = $this->mockClassAndInterfaces('ArrayObject', ['Dhii\Collection\MapInterface'])
                    ->setMethods(['get', 'has'])
                    ->enableOriginalConstructor()
                    ->setConstructorArgs([$data])
                    ->getMockForAbstractClass();

        return $map;
    }

    /**
     * Creates a readable state machine instance.
     *
     * @since [*next-version*]
     *
     * @return ReadableStateMachineInterface|MockObject
     */
    public function createReadableStateMachine()
    {
        $mock = $this->getMockBuilder('Dhii\State\ReadableStateMachineInterface')
                     ->setMethods(['transition', 'canTransition', 'getState'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates a state machine factory instance.
     *
     * @since [*next-version*]
     *
     * @return StateMachineFactoryInterface|MockObject
     */
    public function createStateMachineFactory()
    {
        $mock = $this->getMockBuilder('Dhii\State\StateMachineFactoryInterface')
                     ->setMethods(['make'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates a mock state-aware factory instance for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return StateAwareFactoryInterface|MockObject
     */
    public function createStateAwareFactory()
    {
        $mock = $this->getMockBuilder('Dhii\Data\StateAwareFactoryInterface')
                     ->setMethods(['make'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates a mock state-aware instance for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return StateAwareInterface|MockObject
     */
    public function createStateAware()
    {
        $mock = $this->getMockBuilder('Dhii\Data\StateAwareInterface')
                     ->setMethods(['getState'])
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
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\Data\TransitionerInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to ensure that the appropriate setters are invoked for the given arguments.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $transitions       = [
            uniqid('state1-') => [
                uniqid('transition1-') => uniqid('state2-'),
            ],
            uniqid('state2-') => [],
        ];
        $stateAwareFactory = $this->createStateAwareFactory();
        $machineFactory    = $this->createStateMachineFactory();
        $subject           = new BookingTransitioner($transitions, $machineFactory, $stateAwareFactory);
        $reflect           = $this->reflect($subject);

        $this->assertEquals(
            $transitions,
            $reflect->_getTransitions(),
            'Set and retrieved transition containers are not equal.'
        );

        $this->assertSame(
            $machineFactory,
            $reflect->_getStateMachineFactory(),
            'Set and retrieved state machine factories are not the same.'
        );

        $this->assertSame(
            $stateAwareFactory,
            $reflect->_getStateAwareFactory(),
            'Set and retrieved state-aware factories are not the same.'
        );
    }

    /**
     * Tests the transition normalization method to ensure that the transition remains the same.
     *
     * @since [*next-version*]
     */
    public function testNormalizeTransition()
    {
        $subject    = $this->createInstance();
        $reflect    = $this->reflect($subject);
        $booking    = $this->createStateAware();
        $transition = uniqid('transition-');

        $this->assertEquals(
            $transition,
            $reflect->_normalizeTransition($booking, $transition),
            'Input and output transitions are not the same.'
        );
    }

    /**
     * Tests the transition method.
     *
     * @since [*next-version*]
     */
    public function testTransition()
    {
        $stateKey =  uniqid('state-key-');

        // Transition arguments
        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Set up map for state-aware instance
        $currState = uniqid('status-');
        $stateData = ['a' => 1, 'b' => 2, $stateKey => $currState];
        $stateMap  = $this->createMap($stateData);
        $stateAware->method('getState')->willReturn($stateMap);
        // Make map expose status from get()
        $stateMap->expects($this->atLeastOnce())
                 ->method('get')
                 ->with($stateKey)
                 ->willReturn($currState);

        // The transition graph
        $newState    = uniqid('new-state-');
        $transitions = [
            $currState        => [
                $transition => $newState,
            ],
            uniqid('state2-') => [],
            uniqid('state3-') => [],
        ];

        // Set up state machines
        $newMachine = $this->createReadableStateMachine();
        $oldMachine = $this->createReadableStateMachine();
        $oldMachine->expects($this->once())
                   ->method('transition')
                   ->with($transition)
                   ->willReturn($newMachine);
        $newMachine->expects($this->atLeastOnce())
                   ->method('getState')
                   ->willReturn($newState);
        // Set up factory for the first machine
        $machineFactory = $this->createStateMachineFactory();
        $machineFactory->expects($this->once())
                       ->method('make')
                       ->with([
                           'initial_state' => $currState,
                           'transitions'   => $transitions,
                           'event_params'  => [
                               'booking' => $stateAware
                           ]
                       ])
                       ->willReturn($oldMachine);

        // Set up new state-aware instance
        $newStateData  = ['a' => 1, 'b' => 2, $stateKey => $newState];
        $newStateMap   = $this->createMap($stateData);
        $newStateAware = $this->createStateAware();
        $newStateAware->method('getState')->willReturn($newStateMap);

        // Set up state-aware factory to create new instance
        $stateAwareFactory = $this->createStateAwareFactory();
        $stateAwareFactory->expects($this->atLeastOnce())
                          ->method('make')
                          ->with([StateAwareFactoryInterface::K_DATA => $newStateData])
                          ->willReturn($newStateAware);

        // Set up test subject
        $subject = new BookingTransitioner($transitions, $machineFactory, $stateAwareFactory, $stateKey);

        // Run transition and expect returned instance to be the factory-created instance
        $result = $subject->transition($stateAware, $transition);

        $this->assertSame($newStateAware, $result, 'Incorrect state-aware instance returned.');
    }

    /**
     * Tests the transition method with a booking that does not have the state key in its state.
     *
     * @since [*next-version*]
     */
    public function testTransitionNoState()
    {
        $stateKey =  uniqid('state-key-');

        // Transition arguments
        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Set up map for state-aware instance
        $currState = uniqid('status-');
        $stateData = ['a' => 1, 'b' => 2];
        $stateMap  = $this->createMap($stateData);
        $stateAware->method('getState')->willReturn($stateMap);
        // Make map expose status from get()
        $stateMap->expects($this->atLeastOnce())
                 ->method('get')
                 ->with($stateKey)
                 ->willThrowException(new NotFoundException());

        // The transition graph
        $newState    = uniqid('new-state-');
        $transitions = [
            $currState        => [
                $transition => $newState,
            ],
            uniqid('state2-') => [],
            uniqid('state3-') => [],
        ];

        // Set up factory for the first machine and expect it to not get called
        $machineFactory = $this->createStateMachineFactory();
        $machineFactory->expects($this->never())
                       ->method('make');

        // Set up state-aware factory and expect it to not get called
        $stateAwareFactory = $this->createStateAwareFactory();
        $stateAwareFactory->expects($this->never())
                          ->method('make');

        // Set up test subject
        $subject = new BookingTransitioner($transitions, $machineFactory, $stateAwareFactory, $stateKey);

        // Run transition and expect exception
        $this->setExpectedException('Dhii\Data\Exception\TransitionerException');
        $subject->transition($stateAware, $transition);
    }

    /**
     * Tests the "transitioner exception" factory to assert whether a correct exception instance is created.
     *
     * @since [*next-version*]
     */
    public function testCreateTransitionerException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $message  = uniqid('message-');
        $code     = rand();
        $previous = new \Exception();

        try {
            $reflect->_throwTransitionerException($message, $code, $previous);
            $this->fail('Expected exception was not thrown.');
        } catch (TransitionerExceptionInterface $exception) {
        }

        $this->assertInstanceOf(
            'Dhii\Data\Exception\TransitionerExceptionInterface',
            $exception,
            'Created exception does not implement expected interface.'
        );

        $this->assertEquals($message, $exception->getMessage(), 'Exception message is incorrect.');
        $this->assertEquals($code, $exception->getCode(), 'Exception code is incorrect.');
        $this->assertSame($previous, $exception->getPrevious(), 'Inner exception instance is incorrect.');
        $this->assertSame($subject, $exception->getTransitioner(), 'Exception transitioner is incorrect.');
    }

    /**
     * Tests the "could not transition exception" factory to assert whether a correct exception instance is created.
     *
     * @since [*next-version*]
     */
    public function testCreateCouldNotTransitionException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $message    = uniqid('message-');
        $code       = rand();
        $previous   = new \Exception();
        $booking    = $this->createStateAware();
        $transition = uniqid('transition-');

        try {
            $reflect->_throwCouldNotTransitionException(
                $message,
                $code,
                $previous,
                $booking,
                $transition
            );
            $this->fail('Expected exception was not thrown.');
        } catch (CouldNotTransitionExceptionInterface $exception) {
        }

        $this->assertInstanceOf(
            'Dhii\Data\Exception\CouldNotTransitionExceptionInterface',
            $exception,
            'Created exception does not implement expected interface.'
        );

        $this->assertEquals($message, $exception->getMessage(), 'Exception message is incorrect.');
        $this->assertEquals($code, $exception->getCode(), 'Exception code is incorrect.');
        $this->assertSame($previous, $exception->getPrevious(), 'Inner exception instance is incorrect.');
        $this->assertSame($subject, $exception->getTransitioner(), 'Exception transitioner is incorrect.');
        $this->assertSame($booking, $exception->getSubject(), 'Exception subject is incorrect.');
        $this->assertEquals($transition, $exception->getTransition(), 'Exception transition is incorrect.');
    }
}
