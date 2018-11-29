<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Factory\FactoryInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use RebelCode\Bookings\Availability\Util\CreateAvailabilityPeriodCapableTrait;
use RebelCode\Bookings\Availability\Util\SubtractAvailabilityPeriodsCapableTrait;
use stdClass;
use Traversable;

/**
 * An availability implementation that only provides periods from the first child that aren't in subsequent children.
 *
 * This implementation calculates the subtraction of periods in its children. Periods taken from the first child are
 * passed through a subtraction algorithm to produce the periods of time that exclude periods from the remaining
 * children availabilities.
 *
 * @since [*next-version*]
 */
class SubtractiveAvailability implements AvailabilityInterface
{
    /* @since [*next-version*] */
    use SubtractAvailabilityPeriodsCapableTrait;

    /* @since [*next-version*] */
    use CreateAvailabilityPeriodCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The children availabilities.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityInterface[]|stdClass|Traversable
     */
    protected $children;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityInterface[]|stdClass|Traversable $children      The children availabilities.
     * @param FactoryInterface|null                        $periodFactory Optional availability period factory to use.
     */
    public function __construct($children = [], FactoryInterface $periodFactory = null)
    {
        $this->children = $this->_normalizeIterable($children);

        $this->_setAvPeriodFactory($periodFactory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getAvailablePeriods(PeriodInterface $range)
    {
        // Return no periods if this availability has no children
        if (count($this->children) === 0) {
            return [];
        }

        $results = null;

        foreach ($this->children as $child) {
            // Get the child's periods
            $cPeriods = $child->getAvailablePeriods($range);

            // If this is the first pass, use these periods as the temporary results
            if ($results === null) {
                $results = $cPeriods;
                continue;
            }

            // Prepare a new results list
            $newResults = [];
            // Iterate the existing result periods to apply subtraction to each
            foreach ($results as $rPeriod) {
                // The periods to be subjected to subtraction.
                // Initially this will be just the current iteration period from the existing result set.
                // The below iteration will go through every period from the current child and subtract each period
                // from these "subjected" periods. On each iteration, these subjected periods may grow in number but
                // smaller in duration.
                $sPeriods = [$rPeriod];
                foreach ($cPeriods as $cPeriod) {
                    foreach ($sPeriods as $sPeriod) {
                        // Subtract the child period from the subjected period.
                        // The results are to be the new periods to be subjected to further subtraction
                        $sPeriods = $this->_subtractAvailabilityPeriods($sPeriod, $cPeriod);
                        // Stop prematurely if there is nothing left to subtract from
                        if (empty($sPeriods)) {
                            break 2;
                        }
                    }
                }
                // Merge whatever remains of the period into the new result set
                if (!empty($sPeriods)) {
                    $newResults = array_merge($newResults, $sPeriods);
                }
            }

            // Copy the new results list into the real results list
            $results = $newResults;
        }

        return $results;
    }
}
