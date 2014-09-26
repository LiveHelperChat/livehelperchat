<?php
/**
 * File containing the ezcCacheApcBackend class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This backend stores data in an APC cache.
 *
 * @apichange This class will be deprecated in the next major version of the
 *            Cache component. Please do not use it directly, but use {@link
 *            ezcCacheStorageApc} instead.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheApcBackend extends ezcCacheMemoryBackend
{
    /**
     * Constructs a new ezcCacheApcBackend object.
     *
     * @throws ezcBaseExtensionNotFoundException
     *         If the PHP apc extension is not installed.
     */
    public function __construct()
    {
        if ( !ezcBaseFeatures::hasExtensionSupport( 'apc' ) )
        {
            throw new ezcBaseExtensionNotFoundException( 'apc', null, "PHP does not have APC support." );
        }
    }

    /**
     * Stores the data $var under the key $key. Returns true or false depending
     * on the success of the operation.
     *
     * @param string $key
     * @param mixed $var
     * @param int $ttl
     * @return bool
     */
    public function store( $key, $var, $ttl = 0 )
    {
        $data = new ezcCacheMemoryVarStruct( $key, $var, $ttl );
        return apc_store( $key, $data, $ttl );
    }

    /**
     * Fetches the data associated with key $key.
     *
     * @param mixed $key
     * @return mixed
     */
    public function fetch( $key )
    {
        $data = apc_fetch( $key );
        return ( is_object( $data ) ) ? $data->var : false;
    }

    /**
     * Deletes the data associated with key $key. Returns true or false depending
     * on the success of the operation.
     *
     * @param string $key
     * @return bool
     */
    public function delete( $key )
    {
        return apc_delete( $key );
    }

    /**
     * Resets the complete backend.
     *
     * Marked private to not expose more of this interface to the user, since
     * this will be removed in future versions.
     * 
     * @return void
     * @access private
     */
    public function reset()
    {
        // Kills the whole user cache
        apc_clear_cache( "user" );
    }

    /**
     * Acquires a lock on the given $key.
     *
     * @param string $key 
     * @param int $waitTime usleep()
     * @param int $maxTime seconds
     */
    public function acquireLock( $key, $waitTime, $maxTime )
    {
        $counter = 0;
        // add() does not replace and returns true on success. $maxTime is
        // obeyed by Memcache expiry.
        while ( apc_add( $key, time(), $maxTime ) === false )
        {
            // Wait for next check
            usleep( $waitTime );
            // Don't check expiry time too frquently, since it requires restoring
            if ( ( ++$counter % 10 === 0 ) && ( time() - (int)apc_fetch( $key ) > $maxTime ) )
            {
                // Release expired lock and place own lock
                apc_store( $key, time(), $maxTime );
                break;
            }
        }
    }

    /**
     * Releases a lock on the given $key. 
     * 
     * @param string $key 
     * @return void
     */
    public function releaseLock( $key )
    {
        apc_delete( $key );
    }
}
?>
