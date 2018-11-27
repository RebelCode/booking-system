<?php

namespace RebelCode\Bookings\Availability;

use DateTime;
use DateTimeZone;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use OutOfRangeException;
use stdClass;
use Traversable;

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

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PeriodInterface            $firstPeriod The first available period.
     * @param int                        $repeatFreq  The repetition frequency, in units.
     * @param int                        $repeatEnd   The date on which repetition ends, as a timestamp.
     * @param DateTimeZone               $timezone    The timezone for accurate date calculation.
     * @param array|stdClass|Traversable $resourceIds The IDs of the resources that are available.
     */
    public function __construct(PeriodInterface $firstPeriod, $repeatFreq, $repeatEnd, $timezone, $resourceIds)
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
        $this->resourceIds  = $this->_normalizeIterable($resourceIds);
    }
}
