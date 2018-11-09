<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Time\PeriodInterface;

abstract class AbstractRepeatingAvailability implements AvailabilityInterface
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
     * Optional first date, as a midnight timestamp.
     *
     * @since [*next-version*]
     *
     * @var int|null
     */
    protected $firstDate;

    /**
     * Optional timestamp of when repetition will end.
     *
     * @since [*next-version*]
     *
     * @var int|null
     */
    protected $repeatEnd;

    protected function _getAvailablePeriods(PeriodInterface $range)
    {
        // Get the first occurrence
        $firstDate = $this->_getFirstOccurrence($range);
        // Stop if there are no occurrences in the given range
        if ($firstDate === null) {
            return [];
        }


    }

    /**
     * Calculates the first occurrence of this availability
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface $range
     *
     * @return int|null The timestamp of the first occurrence, or null if the first occurrence is not in the range.
     */
    protected function _getFirstOccurrence(PeriodInterface $range)
    {
        // If the rule has no first date, use the range start
        if ($this->firstDate === null) {
            return $this->_getOccurrenceStart($range->getStart());
        }

        // If the rule's first date is in the future and not in the range, return no occurrence
        if ($this->firstDate > $range->getEnd()) {
            return null;
        }

        // Search for the first occurrence from the rule's first date
        return $this->_searchForFirstOccurrence($this->firstDate, $range->getEnd());
    }

    /**
     * Searches for the first occurrence between two dates.
     *
     * @since [*next-version*]
     *
     * @param int $startDate The timestamp of the date at which to start the search.
     * @param int $endDate   The timestamp of the date at which to stop searching.
     *
     * @return int The timestamp of the first occurrence.
     */
    abstract protected function _searchForFirstOccurrence($startDate, $endDate);

    /**
     * Retrieves the full timestamp for an occurrence that falls on a specific date.
     *
     * @since [*next-version*]
     *
     * @param int $timestamp The timestamp of the date.
     *
     * @return int The full timestamp fo the occurrence.
     */
    abstract protected function _getOccurrenceStart($timestamp);
}
