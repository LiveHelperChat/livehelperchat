<?php
/**
 * File containing the ezcCacheStorageApcPlain class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This storage implementation stores data in an APC cache.
 *
 * This storage can also be used with {@link ezcCacheStack}. However, APC
 * version 3.0.16 or newer is required for that.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageApcPlain extends ezcCacheStorageApc
{
    /**
     * Fetches data from the cache.
     *
     * @param string $identifier The file to fetch data from
     * @param bool $object return the object and not the clean data
     * @return mixed The fetched data or false on failure
     */
    protected function fetchData( $identifier, $object = false )
    {
        $data = $this->backend->fetch( $identifier );
        if ( is_object( $data ) && $object === false )
        {
            return $data->data;
        }
        if ( is_object( $data ) && $object !== false )
        {
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * Wraps the data in an ezcCacheStorageMemoryDataStruct structure in order
     * to store it.
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted can not be handled by this storage (resource).
     *
     * @param mixed $data Simple type or array
     * @return ezcCacheStorageMemoryDataStruct Prepared data
     */
    protected function prepareData( $data )
    {
        if ( is_resource( $data ) )
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple', 'array', 'object' ) );
        }
        return new ezcCacheStorageMemoryDataStruct( $data, $this->properties['location'] );
    }
}
?>
