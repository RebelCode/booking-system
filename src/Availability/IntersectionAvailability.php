<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Factory\FactoryInterface;
use Dhii\Time\PeriodInterface;
use RebelCode\Bookings\Util\Time\IntersectPeriodsCapableTrait;
use RebelCode\Bookings\Util\Time\Period;
use stdClass;
use Traversable;

/**
 * An availability implementation that provides only the intersecting available periods of its children.
 *
 * This implementation calculates the available periods of time that are common for all of its children availabilities.
 * This is accomplished by iterating over each child and using the previous children's available periods as input for
 * the next child.
 *
 * @since [*next-version*]
 */
class IntersectionAvailability implements AvailabilityInterface
{
    /* @since [*next-version*] */
    use IntersectPeriodsCapableTrait;

    /**
     * The children availabilities.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityInterface[]|stdClass|Traversable
     */
    protected $children;

    /**
     * Optional period factory to use.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface|null
     */
    protected $periodFactory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityInterface[]|stdClass|Traversable $children      The children availabilities.
     * @param FactoryInterface|null                        $periodFactory Optional period factory to use.
     */
    public function __construct($children = [], FactoryInterface $periodFactory = null)
    {
        $this->children      = $children;
        $this->periodFactory = $periodFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getAvailablePeriods(PeriodInterface $range)
    {
        $periods = [$range];

        // Process each availability
        foreach ($this->children as $availability) {
            // Use the previous availabilities' periods as ranges to get the next's available periods
            $newPeriods = [];
            foreach ($periods as $_range) {
                $availPeriods = $availability->getAvailablePeriods($_range);
                $newPeriods   = array_merge($newPeriods, $availPeriods);
            }
            $periods = $newPeriods;
        }

        return $periods;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createPeriod($start, $end)
    {
        if ($this->periodFactory === null) {
            return new Period($start, $end);
        }

        return $this->periodFactory->make([
            'start' => $start,
            'end'   => $end,
        ]);
    }
}
