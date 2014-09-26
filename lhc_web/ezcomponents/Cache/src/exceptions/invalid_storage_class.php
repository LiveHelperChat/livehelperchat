<?php
/**
 * File containing the ezcCacheInvalidStorageClassException.
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when an invalid storage class is used.
 * All storage classes used with the {@link ezcCacheManager}, by creating a
 * cache instance, using {@link ezcCacheManager::createCache()}. If you
 * provide a non-existant storage class or a class that does not derive from
 * {@link ezcCacheStorage}, this exception will be thrown.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheInvalidStorageClassException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheInvalidStorageClassException
     * 
     * @param string $storageClass The invalid storage class.
     * @return void
     */
    function __construct( $storageClass )
    {
        parent::__construct( "'{$storageClass}' is not a valid storage class. Storage classes must extend ezcCacheStorage." );
    }
}
?>
