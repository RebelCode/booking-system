<?php

namespace RebelCode\Bookings\Availability;

use DateTimeZone;
use Dhii\Time\PeriodInterface;

/**
 * An availability that repeats on a weekly basis.
 *
 * This is a simple extension of the daily repeating rule that uses a multiplier of 7 for the repetition frequency.
 *
 * @since [*next-version*]
 */
class WeeklyRepeatingAvailability extends DailyRepeatingAvailability
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __construct(PeriodInterface $firstPeriod, $repeatFreq, $repeatEnd, DateTimeZone $timezone)
    {
        parent::__construct($firstPeriod, $repeatFreq * 7, $repeatEnd, $timezone);
    }
}
