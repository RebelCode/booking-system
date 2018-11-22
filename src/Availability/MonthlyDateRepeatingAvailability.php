<?php

namespace RebelCode\Bookings\Availability;

use DateInterval;
use DateTime;

/**
 * An availability that repeats on a monthly basis on a specific date.
 *
 * @since [*next-version*]
 */
class MonthlyDateRepeatingAvailability extends AbstractFixedRepeatingAvailability
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createRepeatInterval($numUnits)
    {
        return new DateInterval(sprintf('P%dM', $numUnits));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _calculateAdjustment(DateTime $dateTime)
    {
        // First adjustment

        // Get the month date of the first period and the argument date time
        $firstPeriodDate = (int) $this->firstStartDt->format('j');
        $argDate         = (int) $dateTime->format('j');

        // Calculate the difference
        $dateDiff = $firstPeriodDate - $argDate;

        // Calculate adjustment to move the day such that it falls on the correct month date
        if ($dateDiff !== 0) {
            $days           = (int) abs($dateDiff);
            $adjust         = new DateInterval(sprintf('P%dD', $days));
            $adjust->invert = ($dateDiff < 0) ? 1 : 0;

            return $adjust;
        }

        // Second adjustment

        // Calculate month difference and modulo
        $monthDiff = (int) $this->firstStartDt->diff($dateTime)->format('%R%m');
        $modulo    = $monthDiff % $this->repeatFreq;

        // If the month falls onto an occurrence month, no adjustment is needed
        if ($modulo === 0) {
            return null;
        }

        // Calculate the number of months to adjust by
        $months   = (int) floor($this->repeatFreq - $modulo);
        $interval = new DateInterval(sprintf('P%dM', $months));

        return $interval;
    }
}
