<?php
/**
 * File containing the ezcCacheStorageMemcachePlain class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This storage implementation stores data in a Memcache cache.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageMemcachePlain extends ezcCacheStorageMemcache
{
    /**
     * Fetches data from the cache.
     *
     * @param string $identifier The file to fetch data from
     * @param bool $object Return the object and not the clean data
     * @return mixed The fetched data or false on failure
     */
    protected function fetchData( $identifier, $object = false )
    {
        // @TODO: This is also done in the backend, again. However, since the
        // backend is public, too, we need to keep both for now.
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
