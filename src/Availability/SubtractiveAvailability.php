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
            // Iterate the existing result periods and the child periods that were just obtained
            foreach ($results as $rPeriod) {
                foreach ($cPeriods as $cPeriod) {
                    // Subtract the child period from the recorded period, and add to the new results list
                    $iPeriods   = $this->_subtractAvailabilityPeriods($rPeriod, $cPeriod);
                    $newResults = array_merge($newResults, $iPeriods);
                }
            }

            // Copy the new results list into the real results list
            $results = $newResults;
        }

        return $results;
    }
}
