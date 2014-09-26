<?php
/**
 * File containing the ezcCacheMemcacheBackend class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This backend stores data in a Memcache.
 *
 * @apichange This class will be deprecated in the next major version of the
 *            Cache component. Please do not use it directly, but use {@link
 *            ezcCacheStorageMemcache} instead.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheMemcacheBackend extends ezcCacheMemoryBackend
{
    /**
     * The compress threshold.
     *
     * Nearly 1MB (48,576B less).
     */
    const COMPRESS_THRESHOLD = 1000000;

    /**
     * Maximum length of a cache key for Memcached. 
     */
    const MAX_KEY_LENGTH = 249;

    /**
     * Holds an instance to a Memcache object.
     *
     * @var resource
     */
    protected $memcache;

    /**
     * Holds the options for this class.
     *
     * @var ezcCacheStorageMemcacheOptions
     */
    protected $options;

    /**
     * Stores the connections to Memcached.
     *
     * @var array(string=>Memcache)
     */
    protected static $connections = array();

    /**
     * Keeps track of the number of backends using the same connection.
     *
     * This is to avoid that the dtor of a backend accedentally closes a
     * connection that is still in used by another backend.
     *
     * @var array(string=>int)
     */
    protected static $connectionCounter = array();

    /**
     * Stores the connection identifier. 
     *
     * This is generated in the ctor and used in the dtor.
     * 
     * @var string
     */
    protected $connectionIdentifier;

    /**
     * Constructs a new ezcCacheMemcacheBackend object.
     *
     * For options for this backend see {@link ezcCacheStorageMemcacheOptions}.
     *
     * @throws ezcBaseExtensionNotFoundException
     *         If the PHP memcache and zlib extensions are not installed.
     * @throws ezcCacheMemcacheException
     *         If the connection to the Memcache host did not succeed.
     *
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        if ( !ezcBaseFeatures::hasExtensionSupport( 'memcache' ) )
        {
            throw new ezcBaseExtensionNotFoundException( 'memcache', null, "PHP does not have Memcache support." );
        }

        if ( !ezcBaseFeatures::hasExtensionSupport( 'zlib' ) )
        {
            throw new ezcBaseExtensionNotFoundException( 'zlib', null, "PHP not configured with --with-zlib." );
        }

        $this->options = new ezcCacheStorageMemcacheOptions( $options );

        $this->connectionIdentifier = $this->options->host . ':' . $this->options->port;
        if ( !isset( self::$connections[$this->connectionIdentifier] ) )
        {
            self::$connections[$this->connectionIdentifier]     = new Memcache();
            // Currently 0 backends use the connection
            self::$connectionCounter[$this->connectionIdentifier] = 0;
        }

        $this->memcache = self::$connections[$this->connectionIdentifier];
        // Now 1 backend uses it
        self::$connectionCounter[$this->connectionIdentifier]++;
        if ( $this->options->persistent === true )
        {
            if ( !@$this->memcache->pconnect( $this->options->host, $this->options->port, $this->options->ttl ) )
            {
                throw new ezcCacheMemcacheException( 'Could not connect to Memcache using a persistent connection.' );
            }
        }
        else
        {
            if ( !@$this->memcache->connect( $this->options->host, $this->options->port, $this->options->ttl ) )
            {
                throw new ezcCacheMemcacheException( 'Could not connect to Memcache.' );
            }
        }

        $this->memcache->setCompressThreshold( self::COMPRESS_THRESHOLD );
    }

    /**
     * Destructor for the Memcache backend.
     */
    public function __destruct()
    {
        self::$connectionCounter[$this->connectionIdentifier]--;
        // Save to ignore persistent connections, since close() does not affect them
        if ( self::$connectionCounter[$this->connectionIdentifier] === 0 )
        {
            $this->memcache->close();
        }
    }

    /**
     * Adds the $var data to the cache under the key $key. Returns true or
     * false depending on the success of the operation.
     *
     * @param string $key
     * @param mixed $var
     * @param int $expire
     * @return bool
     */
    public function store( $key, $var, $expire = 0 )
    {
        if ( strlen( $key ) > self::MAX_KEY_LENGTH )
        {
            throw new ezcCacheInvalidKeyException( $key, 'Length > ' . self::MAX_KEY_LENGTH . '.' );
        }

        // protect our data by wrapping it in an object
        $data = new ezcCacheMemoryVarStruct( $key, $var, $expire );
        $compressed = ( $this->options->compressed === true ) ? MEMCACHE_COMPRESSED : false;
        return $this->memcache->set( $key, $data, $compressed, $expire );
    }

    /**
     * Returns the data from the cache associated with key $key.
     *
     * @param mixed $key
     * @return mixed
     */
    public function fetch( $key )
    {
        if ( strlen( $key ) > self::MAX_KEY_LENGTH )
        {
            throw new ezcCacheInvalidKeyException( $key, 'Length > ' . self::MAX_KEY_LENGTH . '.' );
        }

        $data = $this->memcache->get( $key );
        return ( is_object( $data ) ) ? $data->var : false;
    }

    /**
     * Deletes the data from the cache associated with key $key. Returns true or
     * false depending on the success of the operation.
     *
     * The value $timeout specifies the timeout period in seconds for the delete
     * command.
     *
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    public function delete( $key, $timeout = 0 )
    {
        if ( strlen( $key ) > self::MAX_KEY_LENGTH )
        {
            throw new ezcCacheInvalidKeyException( $key, 'Length > ' . self::MAX_KEY_LENGTH . '.' );
        }

        return $this->memcache->delete( $key, $timeout );
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
        // Kills whole memcache content
        $this->memcache->flush();
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
        if ( strlen( $key ) > self::MAX_KEY_LENGTH )
        {
            throw new ezcCacheInvalidKeyException( $key, 'Length > ' . self::MAX_KEY_LENGTH . '.' );
        }

        // add() does not replace and returns true on success. $maxTime is
        // obeyed by Memcache expiry.
        while ( $this->memcache->add( $key, $key, false, $maxTime ) === false )
        {
            // Wait for next check
            usleep( $waitTime );
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
        if ( strlen( $key ) > self::MAX_KEY_LENGTH )
        {
            throw new ezcCacheInvalidKeyException( $key, 'Length > ' . self::MAX_KEY_LENGTH . '.' );
        }

        $this->memcache->delete( $key );
    }

}
?>
