<?php

namespace RebelCode\Bookings\Availability;

use DateInterval;
use DateTime;
use Dhii\Time\PeriodInterface;

/**
 * An availability that repeats monthly on a specific nth day of the week (example: the second Monday of every month).
 *
 * @since [*next-version*]
 */
class MonthlyWeekDayRepeatingAvailability extends AbstractFixedRepeatingAvailability
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getNextOccurrence($rangeStart, $rangeEnd, PeriodInterface $previous)
    {
        $prevDt = $this->_createDateTimeFromTimestamp($previous->getStart(), $this->timezone);

        // Get the day of the week of the first period along with its index
        $dayOfWeek  = $this->firstStartDt->format('l');
        $nthOfMonth = (int) ceil($this->firstStartDt->format('j') / 7);

        // Add the required number of months according to the repetition frequency
        // Move the date to the first day of the month (this removes the time)
        // Add N days of the week (example: add 2 Mondays)
        // Add the time back to the date
        $start = clone $prevDt;
        $start->modify(sprintf('+%d months', $this->repeatFreq))
              ->modify('first day of this month')
              ->modify(sprintf('+%d %s', $nthOfMonth, $dayOfWeek))
              ->modify($this->firstStartDt->format('H:i:s'));

        return $this->_createOccurrence($start);
    }

    /**
     * {@inheritdoc}
     *
     * This method is not used for this implementation.
     *
     * @since [*next-version*]
     */
    protected function _createRepeatInterval($numUnits)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _calculateAdjustment(DateTime $dateTime)
    {
        // Get the first period's and the argument's day of the week
        $fDayOfWeek = (int) $this->firstStartDt->format('w');
        $aDayOfWeek = (int) $dateTime->format('w');

        // First adjustment
        if ($fDayOfWeek !== $aDayOfWeek) {
            $dotwDiff  = (int) abs($fDayOfWeek - $aDayOfWeek);
            $dayAdjust = 7 - $dotwDiff;
            $interval  = new DateInterval(sprintf('P%dD', $dayAdjust));

            return $interval;
        }

        // Get first period's and the arguments nth month indexes
        $fNthOfMonth = (int) ceil($this->firstStartDt->format('j') / 7);
        $aNthOfMonth = (int) ceil($dateTime->format('j') / 7);

        // Second adjustment
        if ($fNthOfMonth !== $aNthOfMonth) {
            $weeksAdjust      = $fNthOfMonth - $aNthOfMonth;
            $interval         = new DateInterval(sprintf('P%dW', abs($weeksAdjust)));
            $interval->invert = (int) ($weeksAdjust < 0);

            return $interval;
        }

        // Third adjustment
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
