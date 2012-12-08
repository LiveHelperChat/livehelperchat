<?php
/**
 * File containing the ezcCacheInvalidIdException
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if the given cache ID does not exist.
 * Caches must be created using {@link ezcCacheManager::createCache()} before 
 * they can be access using {@link ezcCacheManager::getCache()}. If you try to
 * access a non-existent cache ID, this exception will be thrown.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheInvalidIdException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheInvalidIdException.
     * 
     * @param string $id The invalid ID.
     * @return void
     */
    function __construct( $id )
    {
        parent::__construct( "No cache or cache configuration known with ID '{$id}'." );
    }
}
?>
