<?php

namespace RebelCode\Bookings\Availability;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use RebelCode\Time\NormalizeTimestampCapableTrait;
use stdClass;
use Traversable;

/**
 * Implementation of a resource availability period.
 *
 * @since [*next-version*]
 */
class AvailabilityPeriod implements AvailabilityPeriodInterface
{
    /* @since [*next-version*] */
    use NormalizeTimestampCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIntCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The timestamp for the when the period starts.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $start;

    /**
     * The timestamp for when the period ends.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $end;

    /**
     * The IDs of the resources that are available during this period.
     *
     * @since [*next-version*]
     *
     * @var int[]|string[]|Stringable[]|stdClass|Traversable
     */
    protected $resourceIds;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param int                                              $start       The timestamp for when the period starts.
     * @param int                                              $end         The timestamp for when the period ends.
     * @param int[]|string[]|Stringable[]|stdClass|Traversable $resourceIds The IDs of the resources that are available
     *                                                                      during this period.
     */
    public function __construct($start, $end, $resourceIds)
    {
        $this->start       = $start;
        $this->end         = $end;
        $this->resourceIds = $resourceIds;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getDuration()
    {
        return $this->end - $this->start;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getResourceIds()
    {
        return $this->resourceIds;
    }
}
