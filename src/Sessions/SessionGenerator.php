<?php

namespace RebelCode\Bookings\Sessions;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Time\PeriodInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use RebelCode\Bookings\Availability\AvailabilityPeriodInterface;
use stdClass;
use Traversable;

/**
 * An implementation of a session generator that generates using session types.
 *
 * This implementation uses the PHP generator approach for low memory usage. The algorithm is optimized as follows:
 * * calculations are all basic integer arithmetic (no datetime or period objects)
 * * iteration count is minimized by detecting redundant passes
 * * function and method calls are kept to a minimum
 * * time lookup is performed using array-keys to avoid O(n) search time
 *
 * @since [*next-version*]
 *
 * @see   SessionTypeInterface
 */
class SessionGenerator implements SessionGeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function generate(AvailabilityPeriodInterface $period, $sessionTypes)
    {
        $start       = $period->getStart();
        $end         = $period->getEnd();
        $resourceIds = $period->getResourceIds();

        // The start times to process (as keys)
        $starts = [$start => 1];
        // Keep track of which start times have already been processed
        $done = [];
        // Keep iterating until there are no more start times to process
        while (count($starts) > 0) {
            // Populate a new array of new start times, so as to not modify the original while iterating it
            $newStarts = [];
            // For each key (start time - the value $_ is unused)
            foreach ($starts as $s => $_) {
                // If this start time has already been processed, continue
                if (isset($done[$s])) {
                    continue;
                }
                // Iterate all session types
                foreach ($sessionTypes as $sessionType) {
                    // Get the next session
                    $session = $sessionType->getSessionAt($s, $period);
                    // If it fits in the range, yield it
                    // And use its end time as a new start time to process
                    if ($session['start'] < $end && $session['end'] <= $end) {
                        $session['resource_ids']    = $resourceIds;
                        $newStarts[$session['out']] = 1;

                        yield $session;
                    }
                }
                // Mark this start time as done
                $done[$s] = 1;
            }
            // Update the start times to process
            $starts = $newStarts;
        }
    }
}
