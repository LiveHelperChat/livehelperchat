<?php
/**
 * File containing the ezcCacheInvalidKeyException
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a certain cache key could not be processed by a backend.
 *
 * The keys used in memory backends (like {@link ezcCacheMemcacheBackend})
 * underly certain validation rules. If one of these rules does not match a
 * key, this exception is thrown.
 * 
 * @package Cache
 * @version 1.5
 */
class ezcCacheInvalidKeyException extends ezcCacheException
{
    /**
     * Creates a new invalid key exception.
     *
     * Indicates that $key is not a valid cache key for a certain storage.
     * $reason specifies what is invalid about the key.
     * 
     * @param string $key 
     * @param string $reason 
     */
    public function __construct( $key, $reason = null )
    {
        parent::__construct(
            "The cache key '$key' is invalid." . ( $reason !== null ? ' Reason: ' . $reason : '' )
        );
    }
}

?>
