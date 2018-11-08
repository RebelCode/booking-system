<?php

namespace RebelCode\Bookings\Util\Time;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use RebelCode\Time\NormalizeTimestampCapableTrait;

/**
 * Implementation of a period of time.
 *
 * @since [*next-version*]
 */
class Period implements PeriodInterface
{
    /* @since [*next-version*] */
    use NormalizeTimestampCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIntCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The start time for this period, as a unix timestamp.
     *
     * @since [*next-version*]
     *
     * @var int|float|string|Stringable
     */
    protected $start;

    /**
     * The end time for this period, as a unix timestamp.
     *
     * @since [*next-version*]
     *
     * @var int|float|string|Stringable
     */
    protected $end;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param int|float|string|Stringable $start The start time for this period, as a unix timestamp.
     * @param int|float|string|Stringable $end   The end time for this period, as a unix timestamp.
     *
     * @throws InvalidArgumentException If one of the arguments is an invalid timestamp value.
     */
    public function __construct($start, $end)
    {
        $this->start = $this->_normalizeTimestamp($start);
        $this->end   = $this->_normalizeTimestamp($end);
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
        return (int) abs($this->end - $this->start);
    }
}
