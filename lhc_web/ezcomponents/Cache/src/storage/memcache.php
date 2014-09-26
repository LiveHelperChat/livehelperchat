<?php
/**
 * File containing the ezcCacheStorageMemcache class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This class is a common base class for all Memcache based storage classes.
 * To implement a Memcache based cache storage, you simply have to derive
 * from this class and implement the {@link ezcCacheStorageMemcache::fetchData()}
 * and {@link ezcCacheStorageMemcache::prepareData()} methods.
 *
 * For example code of using a cache storage, see {@link ezcCacheManager}.
 *
 * The Cache package already contains an implementation this class:
 *  - {@link ezcCacheStorageMemcachePlain}
 *
 * Options for this class are defined in {@link ezcCacheStorageMemcacheOptions}.
 *
 * @package Cache
 * @version 1.5
 */
abstract class ezcCacheStorageMemcache extends ezcCacheStorageMemory
{
    /**
     * The backend name.
     */
    const BACKEND_NAME = "Memcache";

    /**
     * The registry name.
     */
    const REGISTRY_NAME = 'ezcCacheStorageMemcache_Registry';

    /**
     * Creates a new cache storage in the given location.
     *
     * Options can contain the 'ttl' ( Time-To-Life ). This is per default set
     * to 1 day.
     *
     * For details about the options see {@link ezcCacheStorageMemcacheOptions}.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If you tried to set a non-existent option value.
     *
     * @param string $location Path to the cache location
     * @param array(string=>string) $options Options for the cache
     */
    public function __construct( $location = null, array $options = array() )
    {
        parent::__construct( $location, array() );

        // Overwrite parent set options with new ezcCacheMemcacheStorageOptions
        $this->properties['options'] = new ezcCacheStorageMemcacheOptions( $options );

        $this->backend = new ezcCacheMemcacheBackend( array(
            'host' => $this->properties['options']->host,
            'port' => $this->properties['options']->port,
            'ttl' => $this->properties['options']->ttl,
            'persistent' => $this->properties['options']->persistent,
            'compressed' => $this->properties['options']->compressed,
        ) );

        $this->registryName = self::REGISTRY_NAME;
        $this->backendName = self::BACKEND_NAME;
    }

    /**
     * Fetches the data from the cache.
     *
     * @param string $identifier The memcache identifier to fetch data from
     * @return mixed The fetched data or false on failure
     */
    abstract protected function fetchData( $identifier );

    /**
     * Prepare the data for storing.
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted can not be handled by this storage (object,
     *         resource).
     *
     * @param mixed $data Simple type or array
     * @return mixed Prepared data
     */
    abstract protected function prepareData( $data );
    
    /**
     * Property write access.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBaseValueException
     *         If the value for the property options is not an instance of
     *         ezcCacheStorageOptions.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'options':
                $this->setOptions( $val );
                return;
            default:
                parent::__set( $propertyName, $val );
        }
    }

    /**
     * Set new options.
     *
     * Overwrites the options with the given ones.
     * 
     * @param ezcCacheStorageMemcacheOptions $options The options to set.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If you tried to set a non-existent option value. The accepted
     *         options depend on the ezcCacheStorage implementation and may
     *         vary.
     * @throws ezcBaseValueException
     *         If the value is not valid for the desired option.
     * @throws ezcBaseValueException
     *         If you submit neither an array nor an instance of 
     *         ezcCacheStorageOptions.
     *
     * @apichange Use $storage->options instead.
     */
    public function setOptions( $options )
    {
        switch ( true )
        {
            case ( $options instanceof ezcCacheStorageMemcacheOptions ):
                $this->properties['options'] = $options;
                break;
            case ( $options instanceof ezcCacheStorageOptions ):
                $this->properties['options']->mergeStorageOptions( $options );
                break;
            case ( is_array( $options ) ):
                $this->properties['options']->merge( $options );
                break;
            default:
                throw new ezcBaseValueException(
                    'options',
                    $options,
                    'instance of ezcCacheStorageMemcacheOptions'
                );
        }
    }
}
?>
