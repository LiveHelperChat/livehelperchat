<?php
/**
 * File containing the ezcCacheStackReplacementStrategy interface.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Interface to be implemented by stack replacement strategies.
 *
 * This interface is to be implemented by replacement strategy classes, which
 * can be configured to be used by an {@link ezcCacheStack}. The defined
 * methods wrap around their counterparts on {@link ezcCacheStackableStorage}.
 *
 * A replacement strategy must take care about the actual
 * storing/restoring/deleting of cache items in the given storage. In addition
 * it must take care about keeping the needed {@link ezcCacheStackMetaData} up
 * to date and about purging data from the cache storage, if it runs full.
 *
 * A replacement strategy must define its own meta data class which implements
 * {@link ezcCacheStackMetaData}. It must check in each method call, that the
 * given $metaData is of correct type. If this is not the case, {@link
 * ezcCacheInvalidMetaDataException} must be throwen.
 * 
 * @package Cache
 * @version 1.5
 */
interface ezcCacheStackReplacementStrategy
{
    /**
     * Stores the given $itemData in the storage given in $conf.
     *
     * This method stores the given $itemData assigned to $itemId and
     * optionally $itemAttributes in the {@link ezcCacheStackableStorage} given
     * in $conf. In case the storage has reached the $itemLimit defined in
     * $conf, it must be freed according to $freeRate {@link
     * ezcCacheStackStorageConfiguration}.
     *
     * The freeing of items from the storage must first happen via {@link
     * ezcCacheStackableStorage::purge()}, which removes outdated items from
     * the storage and returns the affected IDs. In case this does not last to
     * free the desired number of items, the replacement strategy specific
     * algorithm for freeing takes effect.
     *
     * After the necessary freeing process has been performed, the item is
     * stored in the storage and the $metaData is updated accordingly.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param mixed $itemData
     * @param array(string=>string) $itemAttributes
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not processable by this replacement
     *         strategy.
     */
    public static function store(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemData,
        $itemAttributes = array()
    );

    /**
     * Restores the data with the given $dataId from the storage given in $conf.
     *
     * This method takes care of restoring the item with ID $itemId and
     * optionally $itemAttributes from the {@link ezcCacheStackableStorage}
     * given in $conf. The parameters $itemId, $itemAttributes and $search are
     * forwarded to {@link ezcCacheStackableStorage::restore()}, the returned
     * value (item data on successful restore, otherwise false) are returned by
     * this method.
     *
     * The method must take care that the restore process is reflected in
     * $metaData according to the spcific replacement strategy implementation.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param array(string=>string) $itemAttributes
     * @param bool $search
     *
     * @return mixed Restored data or false.
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not processable by this replacement
     *         strategy.
     */
    public static function restore(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemAttributes = array(),
        $search = false
    );

    /**
     * Deletes the data with the given $itemId from the given $storage.
     *
     * This method takes care about deleting the item identified by $itemId and
     * optionally $itemAttributes from the {@link ezcCacheStackableStorage}
     * give in $conf. The parameters $itemId, $itemAttributes and $search are
     * therefore forwarded to {@link ezcCacheStackableStorage::delete()}. This
     * method returns a list of all item IDs that have been deleted by the
     * call. The method reflects these changes in $metaData.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param array(string=>string) $itemAttributes
     * @param bool $search
     *
     * @return array(string) Deleted item IDs.
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not processable by this replacement
     *         strategy.
     */
    public static function delete(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemAttributes = array(),
        $search = false
    );

    /**
     * Returns a fresh meta data object.
     *
     * Different replacement strategies will use different meta data classes.
     * This method must return a freshly created instance of the meta data
     * object used by this meta data.
     * 
     * @return ezcCacheStackMetaData
     */
    public static function createMetaData();
}

?>
