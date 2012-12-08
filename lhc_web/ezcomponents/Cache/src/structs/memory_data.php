<?php
/**
 * File containing the ezcCacheStorageMemoryDataStruct class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Defines a memory data storage structure.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageMemoryDataStruct extends ezcBaseStruct
{
    /**
     * Holds the actual data.
     *
     * @var mixed
     */
    public $data;

    /**
     * Holds the time the data was introduced in the cache.
     *
     * @var string
     */
    public $time;

    /**
     * Holds the location of the cache.
     *
     * @var string
     */
    public $location;

    /**
     * Constructs a new ezcCacheMemoryDataStruct.
     *
     * @param mixed $data
     * @param string $location
     */
    public function __construct( $data, $location )
    {
        $this->data = $data;
        $this->location = $location;
        $this->time = time();
    }
}
?>
