<?php

namespace RebelCode\Bookings\Resources;

use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Common functionality for awareness of resource IDs.
 *
 * @since [*next-version*]
 */
trait ResourceIdsAwareTrait
{
    /**
     * The resource IDs.
     *
     * @since [*next-version*]
     *
     * @var array|stdClass|Traversable
     */
    protected $resourceIds;

    /**
     * Retrieves the resource IDs associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|stdClass|Traversable The iterable list of resource IDs.
     */
    protected function _getResourceIds()
    {
        return $this->resourceIds;
    }

    /**
     * Sets the resource IDs for this instance.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $resourceIds The resource IDs.
     *
     * @throws InvalidArgumentException If the argument is not a valid iterable.
     */
    protected function _setResourceIds($resourceIds)
    {
        $this->resourceIds = $this->_normalizeIterable($resourceIds);
    }

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
