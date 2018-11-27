<?php

namespace RebelCode\Bookings\Availability\Util;

use RebelCode\Bookings\Availability\AvailabilityPeriodInterface;
use stdClass;
use Traversable;

/**
 * Functionality for subtracting an availability period from another.
 *
 * @since [*next-version*]
 */
trait SubtractAvailabilityPeriodsCapableTrait
{
    /**
     * Subtracts an availability period from another, both in terms of time and resources.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityPeriodInterface $p1 The period to subtract p2 from.
     * @param AvailabilityPeriodInterface $p2 The period to be subtracted from p1.
     *
     * @return AvailabilityPeriodInterface[] The resulting periods after subtracting p2 from p1.
     */
    protected function _subtractAvailabilityPeriods(AvailabilityPeriodInterface $p1, AvailabilityPeriodInterface $p2)
    {
        $s1 = $p1->getStart();
        $e1 = $p1->getEnd();
        $s2 = $p2->getStart();
        $e2 = $p2->getEnd();

        // If the two periods do not intersect, return p1 without subtraction
        if (($s1 < $s2 && $e1 < $s2) || ($s2 < $s1 && $e2 < $s1)) {
            return [$p1];
        }

        // If p2 completely contains p1, return no periods
        if ($s2 < $s1 && $e2 > $e1) {
            return [];
        }

        $periods   = [];
        $resources = array_diff($p1->getResourceIds(), $p2->getResourceIds());

        // If p2 starts during p1, create a period from the start of p1 till the start of p2
        // p1 [---------]
        // p2     [--..
        // r  [---]
        if ($s1 < $s2 && $s2 < $e1) {
            $periods[] = $this->_createAvailabilityPeriod($s1, $s2, $resources);
        }

        // If p2 ends during p1, create a period from the end of p2 till the end of p1
        // p1 [---------]
        // p2   ..--]
        // r        [---]
        if ($e1 > $e2 && $e2 > $s1) {
            $periods[] = $this->_createAvailabilityPeriod($e2, $e1, $resources);
        }

        return $periods;
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
