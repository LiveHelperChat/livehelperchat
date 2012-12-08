<?php
/**
 * File containing the ezcCacheStorageApc class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This class is a common base class for all APC based storage classes.
 * To implement an APC based cache storage, you simply have to derive
 * from this class and implement the {@link ezcCacheStorageApc::fetchData()}
 * and {@link ezcCacheStorageApc::prepareData()} methods.
 *
 * For example code of using a cache storage, see {@link ezcCacheManager}.
 *
 * The Cache package already contains these implementations of this class:
 *  - {@link ezcCacheStorageApcPlain}
 *  - {@link ezcCacheStorageFileApcArray}
 *
 * This storage acan also be used with {@link ezcCacheStack}. However, APC
 * version 3.0.16 or newer is required for that.
 *
 * @package Cache
 * @version 1.5
 */
abstract class ezcCacheStorageApc extends ezcCacheStorageMemory
{
    /**
     * The backend name.
     */
    const BACKEND_NAME = "Apc";

    /**
     * The registry name.
     */
    const REGISTRY_NAME = 'ezcCacheStorageApc_Registry';

    /**
     * Creates a new cache storage in the given location.
     *
     * Options can contain the 'ttl' (Time-To-Live). This is per default set
     * to 1 day.
     *
     * For details about the options see {@link ezcCacheStorageApcOptions}.
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

        // Overwrite parent set options with new ezcCacheStorageApcOptions
        $this->properties['options'] = new ezcCacheStorageApcOptions( $options );

        $this->backend = new ezcCacheApcBackend();
        $this->registryName = self::REGISTRY_NAME;
        $this->backendName = self::BACKEND_NAME;
    }

    /**
     * Fetches data from the cache.
     *
     * @param string $identifier The APC identifier to fetch data from
     * @return mixed The fetched data or false on failure
     */
    abstract protected function fetchData( $identifier );

    /**
     * Prepares the data for storing.
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
     * Return the currently set options. 
     *
     * Return the currently set options. The options are returned on an array 
     * that has the same format as the one passed to 
     * {@link ezcCacheStorage::setOptions()}. The possible options for a storage
     * depend on its implementation. 
     * 
     * @param ezcCacheStorageOptions $options 
     *
     * @apichange Use $storage->options instead.
     */
    public function setOptions( $options )
    {
        switch ( true )
        {
            case ( $options instanceof ezcCacheStorageApcOptions ):
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
                    'instance of ezcCacheStorageApcOptions'
                );
        }
    }
}
?>
