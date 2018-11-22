<?php

namespace RebelCode\Bookings\Sessions;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;

/**
 * A simple session type that yields sessions with a fixed duration.
 *
 * This implementation optionally allows the use of a callback for modifying the created sessions. This is useful for
 * making time adjustments to sessions or attaching additional data to the sessions.
 *
 * @since [*next-version*]
 */
class FixedDurationSessionType implements SessionTypeInterface
{
    /* @since [*next-version*] */
    use NormalizeIntCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The duration of sessions, in seconds.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $duration;

    /**
     * A filter callback to invoke when a session is created.
     *
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $callback;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param int           $duration The duration of sessions, in seconds.
     * @param callable|null $callback Optional filter callback to invoke when a session is created.
     *                                The callback will receive the array session as the first argument (having four
     *                                keys: "in", "out", "start" and "end"), as well as the session generation range
     *                                as the second argument.
     */
    public function __construct($duration, callable $callback = null)
    {
        $this->duration = $this->_normalizeInt($duration);
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getSessionAt($time, PeriodInterface $range)
    {
        $endTime = $time + $this->duration;

        $session = [
            'in'    => $time,
            'start' => $time,
            'end'   => $endTime,
            'out'   => $endTime,
        ];

        return ($this->callback === null)
            ? $session
            : call_user_func_array($this->callback, [$session, $range]);
    }
}
