<?php
/**
 * File containing the ezcCacheMemoryBackend class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * An abstract class defining the required methods for memory handlers.
 *
 * Implemented in:
 *  - {@link ezcCacheApcBackend}
 *  - {@link ezcCacheMemcacheBackend}
 *
 * @apichange This class will be deprecated in the next major version of the
 *            Cache component. Please do not use it directly, but use an
 *            implementation of  {@link ezcCacheStorage} instead.
 *
 * @package Cache
 * @version 1.5
 */
abstract class ezcCacheMemoryBackend
{
    /**
     * Stores the data $var under the key $key.
     *
     * @param string $key
     * @param mixed $var
     * @param int $ttl
     * @return bool
     */
    abstract public function store( $key, $var, $ttl = 0 );

    /**
     * Fetches the data associated with key $key.
     *
     * @param string $key
     * @return mixed
     */
    abstract public function fetch( $key );

    /**
     * Deletes the data associated with key $key.
     *
     * @param string $key
     * @return bool
     */
    abstract public function delete( $key );
}
?>
