<?php

namespace RebelCode\Bookings;

use Dhii\Collection\MapInterface;
use Dhii\Data\AbstractBaseStateAware;
use Dhii\Data\StateAwareInterface;
use Dhii\Data\TransitionerInterface;
use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Exception\InternalException;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RebelCode\Time\NormalizeTimestampCapableTrait;

/**
 * Implementation of a booking that is aware of a state map.
 *
 * The state awareness allows the booking to expose all of its data in the form of a map, which in turn allows
 * the booking to have non-standard data attached to it. Furthermore, instances of this class qualify as objects that
 * can be transitioned using a generic transitioner.
 *
 * @since [*next-version*]
 *
 * @see   StateAwareInterface
 * @see   TransitionerInterface
 */
class Booking extends AbstractBaseStateAware implements BookingInterface, StateAwareInterface
{
    /* @since [*next-version*] */
    use NormalizeTimestampCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIntCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateInternalExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The key for the ID in the state map.
     *
     * @since [*next-version*]
     */
    const K_STATE_ID = 'id';

    /**
     * The key for the start time in the state map.
     *
     * @since [*next-version*]
     */
    const K_STATE_START = 'start';

    /**
     * The key for the end time in the state map.
     *
     * @since [*next-version*]
     */
    const K_STATE_END = 'end';

    /**
     * The key for the resource ID in the state map.
     *
     * @since [*next-version*]
     */
    const K_STATE_RESOURCE_IDS = 'resource_ids';

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param MapInterface $state The state map.
     */
    public function __construct(MapInterface $state)
    {
        $this->_init($state);
    }

    /**
     * Reads data from state, defaulting to a value if the data was not found.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $key     The key of the data to read.
     * @param mixed|null             $default The value to return if the key was not found.
     *
     * @return mixed|null The value that corresponds to the $key, or the $default value if the $key was not found.
     *
     * @throws InternalException If an error occurred while attempting to read the data in the state.
     */
    protected function _getFromState($key, $default = null)
    {
        try {
            return $this->_getState()->get($key);
        } catch (NotFoundExceptionInterface $nfException) {
            return $default;
        } catch (ContainerExceptionInterface $cException) {
            throw $this->_createInternalException(
                $this->__('Failed to read "%1$s" data from state', [$key]),
                null,
                $cException
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException
     */
    public function getId()
    {
        return $this->_getFromState(static::K_STATE_ID);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException If an internal error occurred while reading the data from the state.
     */
    public function getStart()
    {
        return $this->_getFromState(static::K_STATE_START, null);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException If an internal error occurred while reading the data from the state.
     */
    public function getEnd()
    {
        return $this->_getFromState(static::K_STATE_END, null);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException If an internal error occurred while reading the data from the state.
     */
    public function getDuration()
    {
        $start = $this->getStart();
        $end   = $this->getEnd();

        return ($start !== null && $end !== null)
            ? $end - $start
            : null;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException If an internal error occurred while reading the data from the state.
     */
    public function getResourceIds()
    {
        return $this->_getFromState(static::K_STATE_RESOURCE_IDS, []);
    }
}
