<?php

namespace RebelCode\Bookings\Availability;

use DateInterval;
use DateTime;

/**
 * An availability that repeats on a daily basis.
 *
 * @since [*next-version*]
 */
class DailyRepeatingAvailability extends AbstractFixedRepeatingAvailability
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createRepeatInterval($numUnits)
    {
        return new DateInterval(sprintf('P%dD', $numUnits));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _calculateAdjustment(DateTime $dateTime)
    {
        // Calculate the difference between the first and the current date times
        $diff = (int) $this->firstStartDt->diff($dateTime)->format('%R%a');
        // Calculate the modulo, which gives the number of days by which the arg is off
        $modulo = $diff % $this->repeatFreq;

        // If arg date is not off, return no adjustment
        if ($modulo === 0) {
            return null;
        }

        // Calculate adjustment as the number of days by which the arg date is off
        $days     = (int) floor($this->repeatFreq - $modulo);
        $interval = new DateInterval(sprintf('P%dD', $days));

        return $interval;
    }
}
