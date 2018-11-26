<?php

namespace RebelCode\Bookings\Availability\Util;

use Dhii\Factory\FactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception;
use InvalidArgumentException;
use RebelCode\Bookings\Availability\AvailabilityPeriod;
use RebelCode\Bookings\Availability\AvailabilityPeriodInterface;
use stdClass;
use Traversable;

/**
 * Common functionality for creating availability periods.
 *
 * @since [*next-version*]
 */
trait CreateAvailabilityPeriodCapableTrait
{
    /**
     * Optional factory for creating availability periods.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface|null
     */
    protected $avPeriodFactory;

    /**
     * Retrieves the availability period factory associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface|null The availability period factory instance, if any.
     */
    protected function _getAvPeriodFactory()
    {
        return $this->avPeriodFactory;
    }

    /**
     * Sets the availability period factory for this instance.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface|null $avPeriodFactory The availability period factory instance or null.
     */
    protected function _setAvPeriodFactory($avPeriodFactory)
    {
        if ($avPeriodFactory !== null && !($avPeriodFactory instanceof FactoryInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an availability period factory'), null, null, $avPeriodFactory
            );
        }

        $this->avPeriodFactory = $avPeriodFactory;
    }

    /**
     * Creates an availability period instance.
     *
     * @since [*next-version*]
     *
     * @param int                        $start       The timestamp for when the period starts.
     * @param int                        $end         The timestamp for when the period ends.
     * @param array|stdClass|Traversable $resourceIds The IDs of the resources that are available.
     *
     * @return AvailabilityPeriodInterface The created availability period instance.
     */
    protected function _createAvailabilityPeriod($start, $end, $resourceIds)
    {
        if ($this->avPeriodFactory === null) {
            return new AvailabilityPeriod($start, $end, $resourceIds);
        }

        return $this->avPeriodFactory->make([
            'start'        => $start,
            'end'          => $end,
            'resource_ids' => $resourceIds,
        ]);
    }

    /**
     * Creates a new Dhii invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param Exception|null                        $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        Exception $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     * @see   _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
