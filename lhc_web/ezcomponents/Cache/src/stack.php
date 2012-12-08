<?php
/**
 * File containing the ezcCacheStack class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Hierarchical caching class using multiple storages.
 *
 * An instance of this class can be used to achieve so called "hierarchical
 * caching". A cache stack consists of an arbitrary number of cache storages,
 * being sorted from top to bottom. Usually this order reflects the speed of
 * access for the caches: The fastest cache is at the top, the slowest at the
 * bottom. Whenever data is stored in the stack, it is stored in all contained
 * storages. When data is to be restored, the stack will restore it from the
 * highest storage it is found in. Data is removed from a storage, whenever the
 * storage reached a configured number of items, using a {@link
 * ezcCacheStackReplacementStrategy}.
 *
 * To create a cache stack, multiple storages are necessary. The following
 * examples assume that $storage1, $storage2 and $storage3 contain objects that
 * implement the {@link ezcCacheStackableStorage} interface.
 *
 * The slowest cache should be at the very bottom of the stack, so that items
 * don't need to be restored from it that frequently.
 * <code>
 * <?php
 *  $stack = new ezcCacheStack( 'stack_id' );
 *  $stack->pushStorage(
 *      new ezcCacheStackStorageConfiguration(
 *          'filesystem_cache',
 *          $storage1,
 *          1000000,
 *          .5
 *      )
 *  );
 * ?>
 * </code>
 * This operations create a new cache stack and add $storage1 to its very
 * bottom. The first parameter for {@linke ezcCacheStackStorageConfiguration}
 * are a unique ID for the storage inside the stack, which should never change.
 * The second parameter is the storage object itself. Parameter 3 is the
 * maximum number of items the storage might contain. As soon as this limit ist
 * reached, 500000 items will be purged from the cache. The latter number is
 * defined through the fourth parameter, indicating the fraction of the stored
 * item number, that is to be freed. For freeing, first already outdated items
 * will be purged. If this does not last, more items will be freed using the
 * {@link ezcCacheStackReplacementStrategy} used by the stack.
 *
 * The following code adds the other 2 storages to the stack, where $storage2
 * is a memory storage {@link ezcCacheStackMemoryStorage} and $storage3 is a
 * custom storage implementation, that stores objects in the current requests
 * memory.
 * <code>
 * <?php
 *  $stack->pushStorage(
 *      new ezcCacheStackStorageConfiguration(
 *          'apc_storage',
 *          $storage2,
 *          10000,
 *          .8
 *      )
 *  );
 *  $stack->pushStorage(
 *      new ezcCacheStackStorageConfiguration(
 *          'custom_storage',
 *          $storage3,
 *          50,
 *          .3
 *      )
 *  );
 * ?>
 * </code>
 * The second level of the cache build by $storage2. This storage will contain
 * 10000 items maximum and when it runs full, 8000 items will be deleted from
 * it. The top level of the cache is the custom cache storage, that will only
 * contain 50 objects in the currently processed request. If this storage ever
 * runs full, which should not happen within a request, 15 items will be
 * removed from it.
 *
 * Since the top most storage in this example does not implement {@link
 * ezcCacheStackMetaDataStorage}, another storage must be defined to be used
 * for storing meta data about the stack:
 * <code>
 * <?php
 *  $stack->options->metaStorage = $storage2;
 *  $stack->options->replacementStrategy = 'ezcCacheStackLfuReplacementStrategy';
 * ?>
 * </code>
 * $storage2 is defined to store the meta information for the stack. In
 * addition, a different replacement strategy than the default {@link
 * ezcCacheStackLruReplacementStrategy} replacement strategy is defined. LFU
 * removes least frequently used items, while LRU deletes least recently used
 * items from a storage, if it runs full.
 *
 * Using the $bubbleUpOnRestore option you can determine, that data which is
 * restored from a lower storage will be automatically stored in all higher
 * storages again. The problem with this is, that only the attributes that are
 * used for restoring will be assigned to the item in higher storages. In
 * addition, the items will be stored with a fresh TTL. Therefore, this
 * behavior is not recommended.
 *
 * If you want to use a cache stack in combination with {@ezcCacheManager}, you
 * will most likely want to use {@link ezcCacheStackConfigurator}. This allows
 * you to let the stack be configured on the fly, when it is used for the first
 * time in a request.
 *
 * Beware, that whenever you change the structure of your stack or the
 * replacement strategy between 2 requests, you should always perform a
 * complete {@link ezcCacheStack::reset()} on it. Otherwise, your cache data
 * might be seriously broken which will result in undefined behaviour of the
 * stack. Changing the replacement strategy will most possibly result in an
 * {@link ezcCacheInvalidMetaDataException}.
 * 
 * @property ezcCacheStackOptions $options
 *           Options for the cache stack.
 * @property string $location
 *           Location of this stack. Unused.
 * @mainclass
 * @package Cache
 * @version 1.5
 */
class ezcCacheStack extends ezcCacheStorage
{
    /**
     * Stack of storages. 
     * 
     * @var array(int=>ezcCacheStackableStorage)
     */
    private $storageStack = array();

    /**
     * Mapping if IDs to storages.
     *
     * Mainly used to validate an ID is not taken twice and a storage is not
     * added twice.
     * 
     * @var array(string=>ezcCacheStackableStorage)
     */
    private $storageIdMap = array();

    /**
     * Creates a new cache stack.
     *
     * Usually you will want to use the {@link ezcCacheManager} to take care of
     * your caches. The $options ({@link ezcCacheStackOptions}) stored in the
     * manager and given here can contain a class reference to an
     * implementation of {@link ezcCacheStackConfigurator} that will be used to
     * initially configure the stack instance directly after construction.
     *
     * To perform manual configuration of the stack the {@link
     * ezcCacheStack::pushStorage()} and {@link ezcCacheStack::popStorage()}
     * methods can be used.
     *
     * The location can be a free form string that identifies the stack
     * uniquely in the {@link ezcCacheManager}. It is currently not used
     * internally in the stack.
     * 
     * @param string $location 
     * @param ezcCacheStackOptions $options 
     */
    public function __construct( $location, ezcCacheStackOptions $options = null )
    {
        if ( $options === null )
        {
            $options = new ezcCacheStackOptions();
        }

        $this->properties['location'] = $location;
        $this->properties['options']  = $options;

        if ( $options->configurator !== null )
        {
            // Configure this instance
            call_user_func(
                array( $options->configurator, 'configure' ),
                $this
            );
        }
    }

    /**
     * Stores data in the cache stack.
     *
     * This method will store the given data across the complete stack. It can
     * afterwards be restored using the {@link ezcCacheStack::restore()} method
     * and be deleted through the {@link ezcCacheStack::delete()} method.
     *
     * The data that can be stored in the cache depends on the configured
     * storages. Usually it is save to store arrays and scalars. However, some
     * caches support objects or do not even support arrays. You need to make
     * sure that the inner caches of the stack *all* support the data you want
     * to store!
     *
     * The $attributes array is optional and can be used to describe the stack
     * further.
     *
     * @param string $id 
     * @param mixed $data 
     * @param array $attributes 
     */
    public function store( $id, $data, $attributes = array() )
    {
        $metaStorage = $this->getMetaDataStorage();
        $metaStorage->lock();
        
        $metaData = $this->getMetaData( $metaStorage );

        foreach( $this->storageStack as $storageConf )
        {
            call_user_func(
                array(
                    $this->properties['options']->replacementStrategy,
                    'store'
                ),
                $storageConf,
                $metaData,
                $id,
                $data,
                $attributes
            );
        }

        $metaStorage->storeMetaData( $metaData );
        $metaStorage->unlock();
    }

    /**
     * Returns the meta data to use.
     *
     * Returns the meta data to use with the configured {@link
     * ezcCacheStackStackReplacementStrategy}.
     * 
     * @param ezcCacheStackMetaDataStorage $metaStorage 
     * @return ezcCacheMetaData
     */
    private function getMetaData( ezcCacheStackMetaDataStorage $metaStorage )
    {
        $metaData = $metaStorage->restoreMetaData();
        
        if ( $metaData === null )
        {
            $metaData = call_user_func(
                array(
                    $this->properties['options']->replacementStrategy,
                    'createMetaData'
                )
            );
        }

        return $metaData;
    }

    /**
     * Restores an item from the stack.
     *
     * This method tries to restore an item from the cache stack and returns
     * the found data, if any. If no data is found, boolean false is returned.
     * Given the ID of an object will restore exactly the desired object. If
     * additional $attributes are given, this may speed up the restore process
     * for some caches. If only attributes are given, the $search parameter
     * makes sense, since it will instruct the inner cach storages to search
     * their content for the given attribute combination and will restore the
     * first item found. However, this process is much slower then restoring
     * with an ID and therefore is not recommended.
     * 
     * @param string $id 
     * @param array $attributes 
     * @param bool $search 
     * @return mixed The restored data or false on failure.
     */
    public function restore( $id, $attributes = array(), $search = false )
    {
        $metaStorage = $this->getMetaDataStorage();
        $metaStorage->lock();

        $metaData = $this->getMetaData( $metaStorage );

        $item = false;
        foreach ( $this->storageStack as $storageConf )
        {
            $item = call_user_func(
                array(
                    $this->properties['options']->replacementStrategy,
                    'restore'
                ),
                $storageConf,
                $metaData,
                $id,
                $attributes,
                $search
            );
            if ( $item !== false )
            {
                if ( $this->properties['options']->bubbleUpOnRestore )
                {
                    $this->bubbleUp( $id, $attributes, $item, $storageConf, $metaData );
                }
                // Found, so end.
                break;
            }
        }

        $metaStorage->storeMetaData( $metaData );
        $metaStorage->unlock();

        return $item;
    }

    /**
     * Bubbles a restored $item up to all storages above $foundStorageConf. 
     * 
     * @param string $id 
     * @param array $attributes 
     * @param mixed $item 
     * @param ezcCacheStackStorageConfiguration $foundStorageConf 
     * @param ezcCacheStackMetaData $metaData
     */
    private function bubbleUp( $id, array $attributes, $item, ezcCacheStackStorageConfiguration $foundStorageConf, ezcCacheStackMetaData $metaData )
    {
        foreach( $this->storageStack as $storageConf )
        {
            if ( $storageConf === $foundStorageConf )
            {
                // This was the storage where we restored
                break;
            }
            call_user_func(
                array(
                    $this->properties['options']->replacementStrategy,
                    'store'
                ),
                $storageConf,
                $metaData,
                $id,
                $item,
                $attributes
            );
        }
    }

    /**
     * Deletes an item from the stack.
     *
     * This method deletes an item from the cache stack. The item will
     * afterwards no more be stored in any of the inner cache storages. Giving
     * the ID of the cache item will delete exactly 1 desired item. Giving an
     * attribute array, describing the desired item in more detail, can speed
     * up the deletion in some caches.
     *
     * Giving null as the ID and just an attribibute array, with the $search
     * attribute in addition, will delete *all* items that comply to the
     * attributes from all storages. This might be much slower than just
     * deleting a single item.
     *
     * Deleting items from a cache stack is not recommended at all. Instead,
     * items should expire on their own or be overwritten with more actual
     * data.
     *
     * The method returns an array containing all deleted item IDs.
     * 
     * @param string $id 
     * @param array $attributes 
     * @param bool $search 
     * @return array(string)
     */
    public function delete( $id = null, $attributes = array(), $search = false )
    {
        $metaStorage = $this->getMetaDataStorage();
        $metaStorage->lock();

        $metaData = $metaStorage->restoreMetaData();

        $deletedIds = array();
        foreach ( $this->storageStack as $storageConf )
        {
            $deletedIds = array_merge(
                $deletedIds,
                call_user_func(
                    array(
                        $this->properties['options']->replacementStrategy,
                        'delete'
                    ),
                    $storageConf,
                    $metaData,
                    $id,
                    $attributes,
                    $search
                )
            );
        }

        $metaStorage->storeMetaData( $metaData );
        $metaStorage->unlock();

        return array_unique( $deletedIds );
    }

    /**
     * Returns the meta data storage to be used.
     *
     * Determines the meta data storage to be used by the stack and returns it.
     *
     * @return ezcCacheStackMetaData
     */
    private function getMetaDataStorage()
    {
        $metaStorage = $this->options->metaStorage;
        if ( $metaStorage === null )
        {
            $metaStorage = reset( $this->storageStack )->storage;
            if ( !( $metaStorage instanceof ezcCacheStackMetaDataStorage ) )
            {
                throw new ezcBaseValueException(
                    'metaStorage',
                    $metaStorage,
                    'ezcCacheStackMetaDataStorage',
                    'top of storage stack'
                );
            }
        }
        return $metaStorage;
    }

    /**
     * Counts how many items are stored, fulfilling certain criteria.
     *
     * This method counts how many data items fulfilling the given criteria are
     * stored overall. Note: The items of all contained storages are counted
     * independantly and summarized.
     * 
     * @param string $id 
     * @param array $attributes 
     * @return int
     */
    public function countDataItems( $id = null, $attributes = array() )
    {
        $sum = 0;
        foreach( $this->storageStack as $storageConf )
        {
            $sum += $storageConf->storage->countDataItems( $id, $attributes );
        }
        return $sum;
    }

    /**
     * Returns the remaining lifetime for the given item ID.
     *
     * This method returns the lifetime in seconds for the item identified by $item
     * and optionally described by $attributes. Definining the $attributes
     * might lead to faster results with some caches.
     *
     * The first internal storage that is found for the data item is chosen to
     * detemine the lifetime. If no storage contains the item or the item is
     * outdated in all found caches, 0 is returned.
     * 
     * @param string $id 
     * @param array $attributes 
     * @return int
     */
    public function getRemainingLifetime( $id, $attributes = array() )
    {
        foreach ( $this->storageStack as $storageConf )
        {
            $lifetime = $storageConf->storage->getRemainingLifetime(
                $id,
                $attributes
            );
            if ( $lifetime > 0 )
            {
                return $lifetime;
            }
        }
        return 0;
    }

    /**
     * Add a storage to the top of the stack.
     *
     * This method is used to add a new storage to the top of the cache. The
     * $storageConf of type {@link ezcCacheStackStorageConfiguration} consists
     * of the actual {@link ezcCacheStackableStorage} and other information.
     *
     * Most importantly, the configuration object contains an ID, which must be
     * unique within the whole storage. The itemLimit setting determines how
     * many items might be stored in the storage at all. freeRate determines
     * which fraction of itemLimit will be freed by the {@link
     * ezcCacheStackStackReplacementStrategy} of the stack, when the storage
     * runs full.
     *
     * @param ezcCacheStackStorageConfiguration $storageConf 
     */
    public function pushStorage( ezcCacheStackStorageConfiguration $storageConf )
    {
        if ( isset( $this->storageIdMap[$storageConf->id] ) )
        {
            throw new ezcCacheStackIdAlreadyUsedException(
                $storageConf->id
            );
        }
        if ( in_array( $storageConf->storage, $this->storageIdMap, true ) )
        {
            throw new ezcCacheStackStorageUsedTwiceException(
                $storageConf->storage
            );
        }
        array_unshift( $this->storageStack, $storageConf );
        $this->storageIdMap[$storageConf->id] = $storageConf->storage;
    }

    /**
     * Removes a storage from the top of the stack.
     *
     * This method can be used to remove the top most {@link
     * ezcCacheStackableStorage} from the stack. This is commonly done to
     * remove caches or to insert new ones into lower positions. In both cases,
     * it is recommended to {@link ezcCacheStack::reset()} the whole cache
     * afterwards to avoid any kind of inconsistency.
     *
     * @return ezcCacheStackStorageConfiguration
     *
     * @throws ezcCacheStackUnderflowException
     *         if called on an empty stack.
     */
    public function popStorage()
    {
        if ( count( $this->storageStack ) === 0 )
        {
            throw new ezcCacheStackUnderflowException();
        }
        $storageConf = array_shift( $this->storageStack );
        unset( $this->storageIdMap[$storageConf->id] );
        return $storageConf;
    }

    /**
     * Returns the number of storages on the stack.
     *
     * Returns the number of storages currently on the stack.
     * 
     * @return int
     */
    public function countStorages()
    {
        return count( $this->storageStack );
    }

    /**
     * Returns all stacked storages.
     *
     * This method returns the whole stack of {@link ezcCacheStackableStorage}
     * as an array. This maybe useful to adjust options of the storages after
     * they have been added to the stack. However, it is not recommended to
     * perform any drastical changes to the configurations. Performing manual
     * stores, restores and deletes on the storages is *highly discouraged*,
     * since it may lead to irrepairable inconsistencies of the stack.
     * 
     * @return array(ezcCacheStackableStorage)
     */
    public function getStorages()
    {
        return $this->storageStack;
    }

    /**
     * Resets the complete stack.
     *
     * This method is used to reset the complete stack of storages. It will
     * reset all storages, using {@link ezcCacheStackableStorage::reset()}, and
     * therefore purge the complete content of the stack. In addition, it will
     * kill the complete meta data.
     *
     * The stack is in a consistent, but empty, state afterwards.
     * 
     * @return void
     */
    public function reset()
    {
        foreach ( $this->storageStack as $storageConf )
        {
            $storageConf->storage->reset();
        }
    }

    /**
     * Validates the $location parameter of the constructor.
     *
     * Returns true, since $location is not necessary for this storage.
     * 
     * @return bool
     */
    protected function validateLocation()
    {
        // Does not utilize $location
        return true;
    }

    /**
     * Sets the options for this stack instance.
     *
     * Overwrites the parent implementation to only allow instances of {@link
     * ezcCacheStackOptions}.
     * 
     * @param ezcCacheStackOptions $options 
     *
     * @apichange Use $stack->options instead.
     */
    public function setOptions( $options )
    {
        // Overloading
        $this->options = $options;
    }
    
    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue  The value for the property.
     *
     * @throws ezcBaseValueException 
     *         If the value for the property options is not an instance of 
     *         ezcCacheStackOptions. 
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName ) 
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcCacheStackOptions ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcCacheStackOptions' );
                }
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
                return;
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
