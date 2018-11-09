<?php

namespace RebelCode\Bookings\Util\Time;

use Dhii\Time\PeriodInterface;

trait IntersectPeriodsCapableTrait
{
    /**
     * Creates a period that represents the intersection of two given periods.
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface $p1 The first period.
     * @param PeriodInterface $p2 The second period.
     *
     * @return PeriodInterface|null The intersection period, or null if the two given periods do not intersect.
     */
    protected function _intersectPeriods(PeriodInterface $p1, PeriodInterface $p2)
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

        return ($end > $start)
            ? $this->_createPeriod($start, $end)
            : null;
    }

    abstract protected function _createPeriod($start, $end);
}
