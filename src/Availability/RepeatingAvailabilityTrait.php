<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Time\PeriodInterface;
use stdClass;
use Traversable;

/**
 * Common functionality for basic repeating availabilities.
 *
 * @since [*next-version*]
 */
trait RepeatingAvailabilityTrait
{
    /**
     * The repetition frequency.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $repeatFreq;

    /**
     * Optional timestamp of when repetition will end.
     *
     * @since [*next-version*]
     *
     * @var int|null
     */
    protected $repeatEnd;

    /**
     * Retrieves the available time periods in a given time range.
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface $range The range of time for which to retrieve available periods.
     *
     * @return PeriodInterface[]|stdClass|Traversable The list of available periods of time.
     */
    protected function _getAvailablePeriods(PeriodInterface $range)
    {
        // Prefetch the range start
        $start = $range->getStart();
        // Calculate the actual end, as either the range end or the repetition end date, whichever comes first
        $end = ($this->repeatEnd === null)
            ? $range->getEnd()
            : min($this->repeatEnd, $range->getEnd());

        $current = $this->_getFirstOccurrence($start, $end);
        $periods = [];

        while ($current !== null) {
            $periods[] = $current;
            $current   = $this->_getNextOccurrence($start, $end, $current);

            if ($current->getStart() > $end) {
                break;
            }
        }

        return $periods;
    }

    /**
     * Retrieves the first occurrence in a given range.
     *
     * @since [*next-version*]
     *
     * @param int $rangeStart The timestamp of the start of the range.
     * @param int $rangeEnd   The timestamp of the end of the range.
     *
     * @return PeriodInterface The next occurrence.
     */
    abstract protected function _getFirstOccurrence($rangeStart, $rangeEnd);

    /**
     * Retrieves the next occurrence in a given range that follows a previous occurrence.
     *
     * @since [*next-version*]
     *
     * @param int             $rangeStart The timestamp of the start of the range.
     * @param int             $rangeEnd   The timestamp of the end of the range.
     * @param PeriodInterface $previous   The previous occurrence. The returned occurrence must occur after this.
     *
     * @return PeriodInterface The next occurrence.
     */
    abstract protected function _getNextOccurrence($rangeStart, $rangeEnd, PeriodInterface $previous);
}
