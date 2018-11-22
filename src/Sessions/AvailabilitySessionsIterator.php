<?php

namespace RebelCode\Bookings\Sessions;

use AppendIterator;
use ArrayIterator;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\NormalizeIteratorCapableTrait;
use Dhii\Time\PeriodInterface;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use RebelCode\Bookings\Availability\AvailabilityInterface;
use stdClass;
use Traversable;

/**
 * An iterator that provides a list of sessions generated within an availability's available periods.
 *
 * @since [*next-version*]
 */
class AvailabilitySessionsIterator implements IteratorAggregate
{
    /* @since [*next-version*] */
    use NormalizeIteratorCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The availability to use to retrieve the available periods for session generation.
     *
     * @since [*next-version*]
     *
     * @var AvailabilityInterface
     */
    protected $availability;

    /**
     * The session generator to use for generating sessions.
     *
     * @since [*next-version*]
     *
     * @var SessionGeneratorInterface
     */
    protected $generator;

    /**
     * The range for which to retrieve the available generation periods.
     *
     * @since [*next-version*]
     *
     * @var PeriodInterface
     */
    protected $range;

    /**
     * The inner iterator.
     *
     * @since [*next-version*]
     *
     * @var array|stdClass|Traversable
     */
    protected $inner;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AvailabilityInterface     $availability The availability to use for retrieving the generation periods.
     * @param SessionGeneratorInterface $generator    The session generator.
     * @param PeriodInterface           $range        The range for which to retrieve the generation periods.
     */
    public function __construct(
        AvailabilityInterface $availability,
        SessionGeneratorInterface $generator,
        PeriodInterface $range
    ) {
        $this->availability = $availability;
        $this->generator    = $generator;
        $this->range        = $range;

        $this->_initInnerIterator();
    }

    /**
     * Initializes the inner iterator.
     *
     * @since [*next-version*]
     */
    protected function _initInnerIterator()
    {
        $this->inner = new AppendIterator();

        foreach ($this->availability->getAvailablePeriods($this->range) as $period) {
            $sessions = $this->generator->generate($period);
            $iterator = $this->_normalizeIterator($sessions);

            $this->inner->append($iterator);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getIterator()
    {
        return $this->inner;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createArrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createTraversableIterator(Traversable $traversable)
    {
        return ($traversable instanceof Iterator)
            ? $traversable
            : new IteratorIterator($traversable);
    }
}
