<?php

namespace RebelCode\Bookings;

use Dhii\Collection\MapFactoryInterface;
use Dhii\Collection\MapInterface;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Data\StateAwareFactoryInterface;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;

/**
 * Implementation of a factory that can create booking instances.
 *
 * @since [*next-version*]
 */
class BookingFactory implements BookingFactoryInterface, StateAwareFactoryInterface
{
    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use ContainerHasCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The map factory, used to create state data maps.
     *
     * @since [*next-version*]
     *
     * @var MapFactoryInterface
     */
    protected $mapFactory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param MapFactoryInterface $mapFactory The map factory, used to create state data maps.
     */
    public function __construct(MapFactoryInterface $mapFactory)
    {
        $this->mapFactory = $mapFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function make($config = null)
    {
        // Read booking data from config
        $data = $config !== null && $this->_containerHas($config, static::K_DATA)
            ? $this->_containerGet($config, static::K_DATA)
            : [];

        // Create state map if necessary
        $state = ($data instanceof MapInterface)
            ? $data
            : $this->mapFactory->make([MapFactoryInterface::K_DATA => $data]);

        return new Booking($state);
    }
}
