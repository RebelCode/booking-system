<?php

namespace RebelCode\Bookings\Availability;

use DateTime;
use DateTimeZone;
use Dhii\Time\PeriodInterface;
use OutOfRangeException;

/**
 * Partial implementation for fixed repeating availabilities.
 *
 * @since [*next-version*]
 */
abstract class AbstractFixedRepeatingAvailability implements AvailabilityInterface
{
    /* @since [*next-version*] */
    use FixedRepeatingAvailabilityTrait {
        _getAvailablePeriods as public getAvailablePeriods;
    }

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface $firstPeriod
     * @param                 $repeatFreq
     * @param                 $repeatEnd
     * @param DateTimeZone    $timezone
     */
    public function __construct(PeriodInterface $firstPeriod, $repeatFreq, $repeatEnd, $timezone)
    {
        if ($repeatFreq === 0) {
            throw new OutOfRangeException('Repetition frequency cannot be zero');
        }

        $this->timezone     = $timezone;
        $this->firstStartDt = new DateTime('@' . $firstPeriod->getStart(), $this->timezone);
        $this->firstEndDt   = new DateTime('@' . $firstPeriod->getEnd(), $this->timezone);
        $this->duration     = $this->firstStartDt->diff($this->firstEndDt);
        $this->repeatFreq   = $repeatFreq;
        $this->repeatEnd    = $repeatEnd;
    }
}
