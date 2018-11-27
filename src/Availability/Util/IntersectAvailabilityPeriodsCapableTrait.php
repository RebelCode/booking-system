<?php

namespace RebelCode\Bookings\Availability\Util;

use RebelCode\Bookings\Availability\AvailabilityPeriodInterface;
use stdClass;
use Traversable;

/**
 * Common functionality for intersecting two availability periods, both in terms of time and resources.
 *
 * @since [*next-version*]
 */
trait IntersectAvailabilityPeriodsCapableTrait
{
    /**
     * Creates an availability period for the time intersection of two periods, with both of their resources.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityPeriodInterface $p1 The first period.
     * @param AvailabilityPeriodInterface $p2 The second period.
     *
     * @return AvailabilityPeriodInterface|null The intersection period, or null if the given periods do not intersect.
     */
    protected function _intersectAvailabilityPeriods(AvailabilityPeriodInterface $p1, AvailabilityPeriodInterface $p2)
    {
        $s1 = $p1->getStart();
        $e1 = $p1->getEnd();
        $s2 = $p2->getStart();
        $e2 = $p2->getEnd();

        $start = ($s1 < $s2)
            ? $s2
            : $s1;

        $end = ($e1 > $e2)
            ? $e2
            : $e1;

        $resourceIds = array_unique(array_merge($p1->getResourceIds(), $p2->getResourceIds()));

        return ($end > $start)
            ? $this->_createAvailabilityPeriod($start, $end, $resourceIds)
            : null;
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
    abstract protected function _createAvailabilityPeriod($start, $end, $resourceIds);
}
