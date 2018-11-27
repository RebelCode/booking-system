<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use InvalidArgumentException;
use RebelCode\Bookings\Availability\Util\CreateAvailabilityPeriodCapableTrait;
use RebelCode\Bookings\Resources\ResourceIdsAwareTrait;
use stdClass;
use Traversable;

/**
 * An availability that wraps around another to provide its availability periods, with additional resources.
 *
 * @since [*next-version*]
 */
class ResourceWrappingAvailability implements AvailabilityInterface
{
    /* @since [*next-version*] */
    use ResourceIdsAwareTrait;

    /* @since [*next-version*] */
    use CreateAvailabilityPeriodCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use NormalizeArrayCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The inner availability to wrap.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityInterface
     */
    protected $innerAvailability;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityInterface      $availability The availability to wrap.
     * @param array|stdClass|Traversable $resourceIds  The resource IDs to add to available periods.
     *
     * @throws InvalidArgumentException If the resource IDs list is not a valid iterable.
     */
    public function __construct(AvailabilityInterface $availability, $resourceIds)
    {
        $this->innerAvailability = $availability;

        $this->_setResourceIds($resourceIds);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getAvailablePeriods(PeriodInterface $range)
    {
        $periods     = $this->innerAvailability->getAvailablePeriods($range);
        $resourceIds = $this->_normalizeArray($this->_getResourceIds());

        foreach ($periods as $period) {
            $newPeriod = $this->_createAvailabilityPeriod(
                $period->getStart(),
                $period->getEnd(),
                array_unique(array_merge($period->getResourceIds(), $resourceIds))
            );

            yield $newPeriod;
        }
    }
}
