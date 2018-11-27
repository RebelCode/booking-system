<?php

namespace RebelCode\Bookings\Availability;

use DateTime;
use DateTimeZone;
use Dhii\Time\PeriodInterface;
use RebelCode\Bookings\Util\Time\Period;
use stdClass;
use Traversable;

/**
 * A composite availability that repeats on a weekly basis on a specific set of days of the week.
 *
 * This implementation is a composite availability that creates {@link WeeklyRepeatingAvailability} instances for each
 * day of the week. The resulting available periods are aggregated from these created children availabilities.
 *
 * @since [*next-version*]
 */
class DotwWeeklyRepeatingAvailability extends CompositeAvailability
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface               $firstPeriod   The first available period.
     * @param string[]|stdClass|Traversable $daysOfTheWeek A list of names of the days of the week to repeat on.
     * @param int                           $repeatFreq    The repetition frequency, in units.
     * @param int                           $repeatEnd     The date on which repetition ends, as a timestamp.
     * @param DateTimeZone                  $timezone      The timezone for accurate date calculation.
     * @param array|stdClass|Traversable    $resourceIds   The IDs of the resources that are available.
     */
    public function __construct(
        PeriodInterface $firstPeriod,
        $daysOfTheWeek,
        $repeatFreq,
        $repeatEnd,
        DateTimeZone $timezone,
        $resourceIds
    ) {
        $availabilities = [];

        // Get the first period's start and end date, along with the duration
        $firstStart = new DateTime('@' . $firstPeriod->getStart(), $timezone);
        $firstEnd   = new DateTime('@' . $firstPeriod->getEnd(), $timezone);
        $duration   = $firstStart->diff($firstEnd);
        // Get the day of the week for the period's start date
        $dotw = (int) $firstStart->format('w');

        // For each day of the week in the list
        foreach ($daysOfTheWeek as $dayOfTheWeek) {
            // Calculate the next occurrence of that day of the week
            $start = clone $firstStart;
            $start->modify(sprintf('next %s', $dayOfTheWeek))
                  ->modify($firstStart->format('H:i:s'));
            // Get its day of the week index
            $dotw2 = (int) $start->format('w');
            // If it's the same as the first period's start date day of the week, use that instead
            // Otherwise the first period's date is always skipped
            $start = ($dotw2 === $dotw)
                ? $firstStart
                : $start;
            // Calculate the end date using the first period's duration
            $end = clone $start;
            $end->add($duration);

            // Create the availability
            $availabilities[] = new WeeklyRepeatingAvailability(
                new Period($start->getTimestamp(), $end->getTimestamp()),
                $repeatFreq,
                $repeatEnd,
                $timezone,
                $resourceIds
            );
        }

        parent::__construct($availabilities);
    }
}
