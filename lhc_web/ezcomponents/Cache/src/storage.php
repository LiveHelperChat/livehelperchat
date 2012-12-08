<?php
/**
 * File containing the ezcCacheStorage class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This is the abstract base class for all cache storages. It provides
 * the interface to be implemented by a cache backend as abstract methods.
 * For your convenience it contains some methods you can utilize to create your
 * own storage implementation (e.g. for storing cache data into a database).
 *
 * Implementations of ezcCacheStorage can be used with the
 * {@link ezcCacheManager} or on their own. If you want to implement a cache
 * storage backend that stores cache data in a file on your harddisk, there
 * is an extended version of ezcCacheStorage, {@link ezcCacheStorageFile},
 * which already implements large parts of the API and leaves only
 * very few work for you to do. The descendant class {@link ezcCacheStorageMemory}
 * is a base class for storage using memory.
 *
 * For example code of using a cache storage, see {@link ezcCacheManager}.
 *
 * The Cache package already contains several implementations of
 * {@link ezcCacheStorageFile}:
 *
 * - {@link ezcCacheStorageFileArray}
 * - {@link ezcCacheStorageFileEvalArray}
 * - {@link ezcCacheStorageFilePlain}
 *
 * Implementations of {@link ezcCacheStorageMemory}:
 *
 * - {@link ezcCacheStorageMemcachePlain}
 * - {@link ezcCacheStorageApcPlain}
 * - {@link ezcCacheStorageFileApcArray}
 *
 * @property ezcCacheStorageOptions $options
 *           Options for the cache storage. Which options are
 *           supported depends on the specific implementation of the
 *           ezcCacheStorage.
 * @property string $location
 *           The location the cache resides in.
 *
 * @package Cache
 * @version 1.5
 */
abstract class ezcCacheStorage
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties;

    /**
     * Creates a new cache storage in the given location.
     * Creates a new cache storage for a given location. The location can
     * differ for each ezcCacheStorage implementation, but will most likely
     * be a filesystem path to a directory where cache data is stored in. The
     * location is null for memory storage and an existing writeable path
     * for file or memory/file storage.
     *
     * Per default there is only 1 common option for all ezcCacheStorage
     * classes, which is the 'ttl' ( Time-To-Life ). This is per default set
     * to 1 day. Specific ezcCacheStorage implementations can have
     * additional options.
     *
     * @param string $location               Path to the cache location. Null for
     *                                       memory-based storage and an existing
     *                                       writeable path for file or memory/file
     *                                       storage.
     * @param array(string=>string) $options Options for the cache.
     *
     * @throws ezcBaseFileNotFoundException
     *         If the storage location does not exist. This should usually not
     *         happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this
     *         exception is thrown, your cache location has been corrupted
     *         after the cache was configured.
     * @throws ezcBaseFileNotFoundException
     *         If the storage location is not a directory. This should usually
     *         not happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this
     *         exception is thrown, your cache location has been corrupted
     *         after the cache was configured.
     * @throws ezcBaseFilePermissionException
     *         If the storage location is not writeable. This should usually not
     *         happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this
     *         exception is thrown, your cache location has been corrupted
     *         after the cache was configured.
     * @throws ezcBasePropertyNotFoundException
     *         If you tried to set a non-existent option value. The accepted
     *         options depend on the ezcCacheStorage implementation and may
     *         vary.
     */
    public function __construct( $location, $options = array() )
    {
        $this->properties['location'] = ( substr( $location, -1 ) === '/' ) ? $location : $location . '/';
        $this->validateLocation();
        $this->properties['options'] = new ezcCacheStorageOptions( $options );
    }

    /**
     * Store data to the cache storage.
     * This method stores the given cache data into the cache, assigning the
     * ID given to it.
     *
     * The type of cache data which is expected by a ezcCacheStorage depends on
     * its implementation. In most cases strings and arrays will be accepted,
     * in some rare cases only strings might be accepted.
     *
     * Using attributes you can describe your cache data further. This allows
     * you to deal with multiple cache data at once later. Some ezcCacheStorage
     * implementations also use the attributes for storage purposes. Attributes
     * form some kind of "extended ID".
     *
     * @param string $id                        The item ID.
     * @param mixed $data                       The data to store.
     * @param array(string=>string) $attributes Attributes that describe the
     *                                          cached data.
     *
     * @return string           The ID of the newly cached data.
     *
     * @throws ezcBaseFilePermissionException
     *         If an already existsing cache file could not be unlinked to
     *         store the new data (may occur, when a cache item's TTL
     *         has expired and the file should be stored with more actual
     *         data). This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     * @throws ezcBaseFilePermissionException
     *         If the directory to store the cache file could not be created.
     *         This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     * @throws ezcBaseFileIoException
     *         If an error occured while writing the data to the cache. If this
     *         exception occurs, a serious error occured and your storage might
     *         be corruped (e.g. broken network connection, file system broken,
     *         ...).
     * @throws ezcCacheInvalidDataException
     *         If the data submitted can not be handled by the implementation
     *         of {@link ezcCacheStorage}. Most implementations can not
     *         handle objects and resources.
     */
    abstract public function store( $id, $data, $attributes = array() );

    /**
     * Restore data from the cache.
     * Restores the data associated with the given cache and
     * returns it. Please see {@link ezcCacheStorage::store()}
     * for more detailed information of cachable datatypes.
     *
     * During access to cached data the caches are automatically
     * expired. This means, that the ezcCacheStorage object checks
     * before returning the data if it's still actual. If the cache
     * has expired, data will be deleted and false is returned.
     *
     * You should always provide the attributes you assigned, although
     * the cache storages must be able to find a cache ID even without
     * them. BEWARE: Finding cache data only by ID can be much
     * slower than finding it by ID and attributes.
     *
     * @param string $id                        The item ID.
     * @param array(string=>string) $attributes Attributes that describe the
     *                                          cached data.
     * @param bool $search                      Whether to search for items
     *                                          if not found directly. Default is
     *                                          false.
     *
     * @return mixed The cached data on success, otherwise false.
     *
     * @throws ezcBaseFilePermissionException
     *         If an already existsing cache file could not be unlinked.
     *         This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     */
    abstract public function restore( $id, $attributes = array(), $search = false );

    /**
     * Delete data from the cache.
     * Purges the cached data for a given ID and or attributes. Using an ID
     * purges only the cache data for just this ID.
     *
     * Additional attributes provided will matched additionally. This can give
     * you an immense speed improvement against just searching for ID ( see
     * {@link ezcCacheStorage::restore()} ).
     *
     * If you only provide attributes for deletion of cache data, all cache
     * data matching these attributes will be purged.
     *
     * @param string $id                        The item ID.
     * @param array(string=>string) $attributes Attributes that describe the
     *                                          cached data.
     * @param bool $search                      Whether to search for items
     *                                          if not found directly. Default is
     *                                          false.
     *
     * @throws ezcBaseFilePermissionException
     *         If an already existsing cache file could not be unlinked.
     *         This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     */
    abstract public function delete( $id = null, $attributes = array(), $search = false );

    /**
    * Return the number of items in the cache matching a certain criteria.
    * This method determines if cache data described by the given ID and/or
    * attributes exists. It returns the number of cache data items found.
    *
    * @param string $id                        The item ID.
    * @param array(string=>string) $attributes Attributes that describe the item
    * @return int The number of cache data items found matching the criteria
    */
    abstract public function countDataItems( $id = null, $attributes = array() );

    /**
     * Returns the time ( in seconds ) that remains for a cache object,
     * before it gets outdated. In case the cache object is already
     * outdated or does not exist, this method returns 0.
     *
     * @param string $id                        The item ID.
     * @param array(string=>string) $attributes Attributes that describe the
     * @access public
     * @return int The remaining lifetime ( 0 if nonexists or outdated ).
     */
    abstract public function getRemainingLifetime( $id, $attributes = array() );

    /**
     * Checks if the location property is valid.
     */
    abstract protected function validateLocation();

    /**
     * Returns the location.
     *
     * Returns the location the current storage resides in. The
     * $location attribute has no setter, since it can only be set during
     * construction.
     *
     * @return string The location of this storage.
     *
     * @apichange Use $stack->location instead.
     */
    public function getLocation()
    {
        return $this->properties['location'];
    }

    /**
     * Return the currently set options.
     *
     * Return the currently set options. The options are returned on an array
     * that has the same format as the one passed to
     * {@link ezcCacheStorage::setOptions()}. The possible options for a storage
     * depend on its implementation.
     *
     * @return ezcCacheStorageOptions The options
     *
     * @apichange Use $storage->options instead.
     */
    public function getOptions()
    {
        return $this->properties['options'];
    }

    /**
     * Set new options.
     *
     * This method allows you to change the options of a cache storage. Change
     * of options take effect directly after this method has been called. The
     * available options depend on the ezcCacheStorage implementation. All
     * implementations have to offer the following options:
     *
     * - ttl        The time-to-life. After this time span, a cache item becomes
     *              invalid and will be purged. The
     *              {@link ezcCacheStorage::restore()} method will then return
     *              false.
     * - extension  The "extension" for your cache items. This is usually the
     *              file name extension, when you deal with file system based
     *              caches or e.g. a database ID extension.
     *
     * @param ezcCacheStorageOptions $options The options to set.
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
        if ( is_array( $options ) )
        {
            $this->properties['options']->merge( $options );
        }
        else if ( $options instanceof ezcCacheStorageOptions )
        {
            $this->properties['options'] = $options;
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "instance of ezcCacheStorageOptions" );
        }
    }


    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     *
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

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
                if ( !( $val instanceof ezcCacheStorageOptions ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'instance of ezcCacheStorageOptions' );
                }
                break;
            case 'location':
                throw new ezcBasePropertyPermissionException(
                    $propertyName,
                    ezcBasePropertyPermissionException::READ
                );
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $val;
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}
?>
