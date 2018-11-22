<?php

namespace RebelCode\Bookings\Util\Time;

use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use RebelCode\Bookings\ResourceIdsAwareInterface;
use stdClass;
use Traversable;

/**
 * Represents a period of time that has resource IDs associated with it.
 *
 * @since [*next-version*]
 */
class ResourceIdsAwarePeriod extends Period implements ResourceIdsAwareInterface
{
    /* @since [*next-version*] */
    use NormalizeArrayCapableTrait;

    /**
     * The resource IDs.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $resourceIds;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $resourceIds The resource IDs.
     */
    public function __construct($start, $end, $resourceIds = [])
    {
        parent::__construct($start, $end);

        $this->resourceIds = $this->_normalizeArray($resourceIds);
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
