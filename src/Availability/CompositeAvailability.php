<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Time\PeriodInterface;
use stdClass;
use Traversable;

/**
 * An availability implementation that is composed of other children availabilities.
 *
 * This availability implementation works out the combined available periods for all of its children. This is
 * accomplished by simply retrieving each child's available periods and combining those results into a single result.
 * Duplicate or overlapping periods are currently not normalized.
 *
 * @since [*next-version*]
 */
class CompositeAvailability implements AvailabilityInterface
{
    /**
     * The children availabilities.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityInterface[]|stdClass|Traversable
     */
    protected $children;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityInterface[]|stdClass|Traversable $children The children availabilities.
     */
    public function __construct($children = [])
    {
        $this->children = $children;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getAvailablePeriods(PeriodInterface $range)
    {
        $periods = [];

        foreach ($this->children as $child) {
            $periods = array_merge($periods, $child->getAvailablePeriods($range));
        }

        return $periods;
    }
}
