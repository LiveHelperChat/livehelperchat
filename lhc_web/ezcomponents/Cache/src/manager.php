<?php
/**
 * File containing the ezcCacheManager class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This is the main class of the Cache package. It gives you a handy interface
 * to create and manage multiple caches at once. It enables you to configure
 * all caches you need in your application in a central place and access them
 * on demand in any place in your application.
 *
 * The use of ezcCacheManager is not required, but recommended. If you only
 * need a few (or maybe just 1) cache instance, you can use and instantiate
 * a {@link ezcCacheStorage} class directly.
 *
 * Usage example for ezcCacheManager:
 * <code>
 * // Some pre-work, needed by the example
 * $basePath = dirname( __FILE__ ).'/cache';
 * function getUniqueId()
 * {
 *     return 'This is a unique ID';
 * }
 *
 * // Central creation and configuration of the caches
 * // The ezcCacheManager just stores the configuration right now and
 * // performs sanity checks. The ezcCacheStorage instances
 * // will be created on demand, when you use them for the first time
 *
 * // Configuration options for a cache (see ezcCacheStorage)
 * $options = array(
 *     'ttl'   => 60*60*24*2,     // Default would be 1 day, here 2 days
 * );
 *
 * // Create a cache named "content", that resides in /var/cache/content
 * // The cache instance will use the ezcCacheStorageFileArray class
 * // to store the cache data. The time-to-live for cache items is set as
 * // defined above.
 * ezcCacheManager::createCache( 'content', $basePath.'/content', 'ezcCacheStorageFileArray', $options );
 *
 * // Create another cache, called "template" in /var/cache/templates.
 * // This cache will use the ezcCacheStorageFilePlain class to store
 * // cache data. It has the same TTL as the cache defined above.
 * ezcCacheManager::createCache( 'template', $basePath.'/templates', 'ezcCacheStorageFilePlain', $options );
 *
 * // Somewhere in the application you can access the caches
 *
 * // Get the instance of the cache called "content"
 * // Now the instance of ezcCacheStorageFileArray is created and
 * // returned to be used. Next time you access this cache, the created
 * // instance will be reused.
 * $cache = ezcCacheManager::getCache( 'content' );
 *
 * // Instead of using the createCache()/getCache() mechanism you can also
 * // create cache on-demand with delayed initialization. You can find
 * // information on how to use that in the tutorial.
 *
 * // Specify any number of attributes to identify the cache item you want
 * // to store. This attributes can be used later to perform operations
 * // on a set of cache items, that share a common attribute.
 * $attributes = array( 'node' => 2, 'area' => 'admin', 'lang' => 'en-GB' );
 *
 * // This function is not part of the Cache package. You have to define
 * // unique IDs for your cache items yourself.
 * $id = getUniqueId();
 *
 * // Initialize the data variable you want to restore
 * $data = '';
 *
 * // Check if data is available in the cache. The restore method returns
 * // the cached data, if available, or bool false.
 * if ( ( $data = $cache->restore( $id, $attributes ) ) === false )
 * {
 *     // The cache item we tried to restore does not exist, so we have to
 *     // generate the data.
 *     $data = array( 'This is some data', 'and some more data.' );
 *     // For testing we echo something here...
 *     echo "No cache data found. Generated some.\n".var_export( $data, true )."\n";
 *     // Now we store the data in the cache. It will be available through
 *     // restore, next time the code is reached
 *     $cache->store( $id, $data, $attributes );
 * }
 * else
 * {
 *     // We found cache data. Let's echo the information.
 *     echo "Cache data found.\n".var_export( $data, true )."\n";
 * }
 *
 * // In some other place you can access the second defined cache.
 * $cache = ezcCacheManager::getCache( 'template' );
 *
 * // Here we are removing cache items. We do not specify an ID (which would
 * // have meant to delete 1 specific cache item), but only an array of
 * // attributes. This will result in all cache items to be deleted, that
 * // have this attribute assigned.
 * $cache->delete( null, array( 'node' => 5 ) );
 * </code>
 *
 * @package Cache
 * @version 1.5
 * @mainclass
 */
class ezcCacheManager
{
    /**
     * Keeps track of the ezcCacheStorage instances.
     * Each cache is created only once per request on the first time it is
     * accessed through {@link ezcCacheManager::getCache()}. Until then,
     * only its configuration is stored in the
     * {@link ezcCacheManager::$configurations} array.
     *
     * @var array(int=>ezcCacheStorage)
     */
    private static $caches = array();

    /**
     * ezcCacheStorage configurations
     * Storage to keep track of ezcCacheStorage configurations. For each
     * configured cache the configuration is initially stored here.
     * {@link ezcCacheStorage} objects are created on first access
     * through {@link ezcCacheManager::getCache()}.
     *
     * @var array(string=>array(string=>string))
     */
    private static $configurations = array();

    /**
     * Private. This class has static methods only.
     *
     * @see ezcCacheManager::createCache()
     * @see ezcCacheManager::getCache()
     */
    private function __construct()
    {
    }

    /**
     * Creates a new cache in the manager.
     * This method is used to create a new cache inside the manager.
     * Each cache has a unique ID to access it during the application
     * runtime. Each location may only be used by 1 cache.
     *
     * The $storageClass parameter must be a subclass of
     * {@link ezcCacheStorage} and tells the manager which object
     * will be used for the cache.
     *
     * The $location parameter depends on the kind of {@link ezcCacheStorage}
     * used for the cache you create. Usually this is a directory on your
     * file system, but may also be e.g. a data source name, if you cache in
     * a database or similar. For memory-based storage ({@link ezcCacheStorageApcPlain}
     * or {@link ezcCacheStorageMemcachePlain}) it is null, but for
     * memory/file hybrid storage ({@link ezcCacheStorageFileApcArray}) it should
     * be an existing writeable path.
     *
     * The $options array consists of several standard attributes and can
     * additionally contain options defined by the {@link ezcCacheStorage}
     * class. Standard options are:
     *
     * <code>
     * array(
     *      'ttl'   => 60*60*24,    // Time-to-life, default: 1 day
     * );
     * </code>
     *
     * @param string $id                     ID of the cache to create.
     * @param string $location               Location to create the cache in. Null for
     *                                       memory-based storage and an existing
     *                                       writeable path for file or memory/file
     *                                       storage.
     * @param string $storageClass           Subclass of {@link ezcCacheStorage}.
     * @param array(string=>string) $options Options for the cache.
     * @return void
     *
     * @throws ezcBaseFileNotFoundException
     *         If the given location does not exist or is not a
     *         directory (thrown by sanity checks performed when storing the
     *         configuration of a cache to ensure the latter calls to
     *         {@link ezcCacheManager::getCache()} do not fail).
     * @throws ezcBaseFilePermissionException
     *         If the given location is not read/writeable (thrown by sanity
     *         checks performed when storing the configuration of a cache to
     *         ensure the latter calls to {@link ezcCacheManager::getCache()}
     *         do not fail).
     * @throws ezcCacheUsedLocationException
     *         If the given location is already in use by another cache.
     * @throws ezcCacheInvalidStorageClassException
     *         If the given storage class does not exist or is no subclass of
     *         ezcCacheStorage.
     */
    public static function createCache( $id, $location = null, $storageClass, $options = array() )
    {
        // BC for missing location. The location should not be missing.
        if ( $location !== null )
        {
            // Unifiy file system locations
            if ( substr( $location, 0, 1 ) === '/' )
            {
                // If non-existent
                if ( ( $realLocation = realpath( $location ) ) === false )
                {
                    throw new ezcBaseFileNotFoundException(
                        $location,
                        'cache location',
                        'Does not exist or is no directory.'
                    );
                }
                $location = $realLocation;
            }

            // Sanity check double taken locations.
            foreach ( self::$configurations as $confId => $config )
            {
                if ( $config['location'] === $location )
                {
                    throw new ezcCacheUsedLocationException( $location, $confId );
                }
            }
        }

        // Sanity check storage class.
        if ( !ezcBaseFeatures::classExists( $storageClass ) || !is_subclass_of( $storageClass, 'ezcCacheStorage' ) )
        {
            throw new ezcCacheInvalidStorageClassException( $storageClass );
        }
        self::$configurations[$id] = array(
            'location' => $location,
            'class'    => $storageClass,
            'options'  => $options,
        );
    }

    /**
     * Returns the ezcCacheStorage object with the given ID.
     * The cache ID has to be defined before using the
     * {@link ezcCacheManager::createCache()} method. If no instance of this
     * cache does exist yet, it's created on the fly. If one exists, it will
     * be reused.
     *
     * @param string $id       The ID of the cache to return.
     * @return ezcCacheStorage The cache with the given ID.
     *
     * @throws ezcCacheInvalidIdException
     *         If the ID of a cache you try to access does not exist. To access
     *         a cache using this method, it first hast to be created using
     *         {@link ezcCacheManager::createCache()}.
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
    public static function getCache( $id )
    {
        // Look for already existing cache object
        if ( !isset( self::$caches[$id] ) )
        {
            // Failed, look for configuration, and if it does not exist, use
            // delayed initialization.
            if ( !isset( self::$configurations[$id] ) )
            {
                ezcBaseInit::fetchConfig( 'ezcInitCacheManager', $id );
            }
            // Check whether delayed initialization actually worked, if not,
            // throw an exception
            if ( !isset( self::$configurations[$id] ) )
            {
                throw new ezcCacheInvalidIdException( $id );
            }
            $class = self::$configurations[$id]['class'];
            self::$caches[$id] = new $class( self::$configurations[$id]['location'], self::$configurations[$id]['options'] );
        }
        return self::$caches[$id];
    }
}
?>
