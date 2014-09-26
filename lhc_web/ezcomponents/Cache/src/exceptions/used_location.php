<?php
/**
 * File containing the ezcCacheUsedLocationException.
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when a given location is already in use.
 * Only one cache may reside in a specific location to avoid conflicts while
 * storing ({@link ezcCacheStorage::store()}) and restoring 
 * ({@link ezcCacheStorage::restore()}) data from a cache. If you try to 
 * configure a cache to be used in location that is already taken by another 
 * cachein ezcCacheManager::createCache(), this exception will be thrown.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheUsedLocationException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheUsedLocationException.
     * 
     * @param string $location The used location.
     * @param string $cacheId  The cache ID using this location.
     * @return void
     */
    function __construct( $location, $cacheId )
    {
        parent::__construct( "Location '{$location}' is already in use by cache with ID '{$cacheId}'." );
    }
}
?>
