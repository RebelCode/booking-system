<?php

namespace RebelCode\Bookings\Availability;

use DateInterval;
use DateTime;
use DateTimeZone;
use Dhii\Time\PeriodInterface;
use RebelCode\Bookings\Util\Time\Period;

/**
 * Common functionality for availabilities that repeat to yield occurrences of fixed durations at fixed intervals.
 *
 * @since [*next-version*]
 */
trait FixedRepeatingAvailabilityTrait
{
    /* @since [*next-version*] */
    use RepeatingAvailabilityTrait;

    /**
     * The date time for the start of the first period.
     *
     * @since [*next-version*]
     *
     * @var DateTime
     */
    protected $firstStartDt;

    /**
     * The date time for the end of the first period.
     *
     * @since [*next-version*]
     *
     * @var DateTime
     */
    protected $firstEndDt;

    /**
     * The duration of an occurrence.
     *
     * @since [*next-version*]
     *
     * @var DateInterval
     */
    protected $duration;

    /**
     * The timezone to use for accurate date time calculation.
     *
     * @since [*next-version*]
     *
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getNextOccurrence($rangeStart, $rangeEnd, PeriodInterface $previous)
    {
        $prevDt = $this->_createDateTimeFromTimestamp($previous->getStart(), $this->timezone);

        $start = clone $prevDt;
        $start->add($this->_createRepeatInterval($this->repeatFreq));

        return $this->_createOccurrence($start);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getFirstOccurrence($rangeStart, $rangeEnd)
    {
        // Use the first period as a starting point if it comes after the param range start
        $rangeStart = max($this->firstStartDt->getTimestamp(), $rangeStart);

        // Create date time for start
        $current = $this->_createDateTimeFromTimestamp($rangeStart, $this->timezone);
        // Ensure time matches that of the first period (since the range start might be an absolute date)
        $timeStr = $this->firstStartDt->format('H:i:s');
        $current->modify($timeStr);

        while ($current->getTimestamp() < $rangeEnd) {
            $adjust = $this->_calculateAdjustment($current);

            if ($adjust === null) {
                return $this->_createOccurrence($current);
            }

            $current->add($adjust);
        }

        return null;
    }

    /**
     * Creates a new occurrence that starts at the given start time.
     *
     * @since [*next-version*]
     *
     * @param DateTime $start The start of the occurrence.
     *
     * @return PeriodInterface The created occurrence.
     */
    protected function _createOccurrence(DateTime $start)
    {
        $end = clone $start;
        $end->add($this->duration);

        return $this->_createPeriod(
            $start->getTimestamp(),
            $end->getTimestamp()
        );
    }

    /**
     * Creates a new period instance.
     *
     * @since [*next-version*]
     *
     * @param int $start The start date and time of the period.
     * @param int $end   The end date and time of the period.
     *
     * @return PeriodInterface The created period instance.
     */
    protected function _createPeriod($start, $end)
    {
        return new Period($start, $end);
    }

    /**
     * Creates a date time instance from a timestamp and timezone.
     *
     * @since [*next-version*]
     *
     * @param int          $timestamp The timestamp.
     * @param DateTimeZone $timezone  The timezone.
     *
     * @return DateTime The created date time.
     */
    protected function _createDateTimeFromTimestamp($timestamp, $timezone)
    {
        return new DateTime('@' . $timestamp, $timezone);
    }

    /**
     * Creates an interval in terms of repetition units.
     *
     * @since [*next-version*]
     *
     * @param int $numUnits The number of repetition units.
     *
     * @return DateInterval The created interval.
     */
    abstract protected function _createRepeatInterval($numUnits);

    /**
     * Calculates the adjustment needed to make a date time fall onto an occurrence.
     *
     * @since [*next-version*]
     *
     * @param DateTime $dateTime The date time to calculate the adjustment for.
     *
     * @return DateInterval|null The interval to apply to the datetime, or null if no adjustment is needed.
     */
    abstract protected function _calculateAdjustment(DateTime $dateTime);
}
