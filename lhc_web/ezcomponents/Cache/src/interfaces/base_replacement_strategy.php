<?php
/**
 * File containing the abstract ezcCacheStackBaseReplacementStrategy class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Base class for LRU and LFU replacement strategies.
 *
 * This class implements the LRU and LFU replacement strategies generically.
 *
 * <ul>
 *  <li>{@link ezcCacheStackLruReplacementStrategy}</li>
 *  <li>{@link ezcCacheStackLfuReplacementStrategy}</li>
 * </ul>
 * are both only wrappers around this class, which implement a different {@link
 * checkMetaData()}, since both strategies use different meta data structures.
 *
 * The (normally abstract static) method
 *  checkMetaData( ezcCacheStackMetaData $metaData );
 * must also be implemented by deriving classes. It must check if the given
 * $metaData is an instance of the correct class. In other cases, an {@link
 * ezcCacheInvalidMetaDataException} must be throwen.
 *
 * For more information on replacement strategies please refer to {@see
 * http://en.wikipedia.org/wiki/Cache_algorithms}.
 *
 * @package Cache
 * @version 1.5
 *
 * @access private
 */
abstract class ezcCacheStackBaseReplacementStrategy implements ezcCacheStackReplacementStrategy
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
     */
    public static function store(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemData,
        $itemAttributes = array()
    )
    {
        if ( !$metaData->hasItem( $conf->id, $itemId )
             && $metaData->reachedItemLimit( $conf->id, $conf->itemLimit ) )
        {
            self::freeData(
                $conf,
                $metaData,
                // Number of items to remove, round() returns float
                (int) round( $conf->freeRate * $conf->itemLimit )
            );
        }
        $conf->storage->store(
            $itemId, $itemData, $itemAttributes
        );
        $metaData->addItem( $conf->id, $itemId );
    }

    /**
     * Frees $freeNum number of item slots in $storage.
     *
     * This method first purges outdated items from the storage inside
     * $conf using {@link ezcCacheStackableStorage::purge()}.
     * If this does not free $freeNum items, least recently used items
     * (determined from {@link ezcCacheStackMetaData}) will be removed from the
     * storage using {@link ezcCacheStackableStorage::delete()}.
     * 
     * @param ezcCacheStackStorageConfiguration $conf 
     * @param ezcCacheStackMetaData $metaData
     * @param int $freeNum
     */
    private static function freeData(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $freeNum
    )
    {
        $purgedIds = $conf->storage->purge();
        // Unset purged items in meta data
        foreach ( $purgedIds as $purgedId )
        {
            $metaData->removeItem( $conf->id, $purgedId );
        }
        $freeNum = $freeNum - count( $purgedIds );

        // Not enough items have been purged, remove manually
        if ( $freeNum > 0 )
        {
            $purgeOrder = $metaData->getReplacementItems();
            foreach ( $purgeOrder as $id => $replacementData )
            {
                // Purge only if available in the current storage
                if ( $metaData->hasItem( $conf->id, $id ) )
                {
                    // Purge all items with this ID, no matter which
                    // attributes, therefore: $search = true.
                    $deletedIds = $conf->storage->delete( $id, null, true );
                    $metaData->removeItem( $conf->id, $id );

                    // Purged enough?
                    if ( --$freeNum == 0 )
                    {
                        break;
                    }
                }
            }
        }
    }

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
    )
    {
        $item = $conf->storage->restore(
            $itemId,
            $itemAttributes,
            $search
        );

        // Update item meta data
        if ( $item === false )
        {
            // Item has been purged / got outdated
            $metaData->removeItem( $conf->id, $itemId );
        }
        else
        {
            // Updates the use time
            $metaData->addItem( $conf->id, $itemId );
        }
        return $item;
    }

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
    )
    {
        $deletedIds = $conf->storage->delete(
            $itemId,
            $itemAttributes,
            $search
        );

        // Possibly deleted multiple items
        foreach ( $deletedIds as $id )
        {
            $metaData->removeItem( $conf->id, $id );
        }
        return $deletedIds;
    }

    /**
     * Pseudo implementation.
     *
     * This method would normally be declared abstract. However, PHP does not
     * allow abstract static methods.
     *
     * Deriving classes must check inside this method, if the given $metaData
     * is appropriate for them. If not, an {@link
     * ezcCacheInvalidMetaDataException} must be throwen.
     * 
     * @param ezcCacheStackMetaData $metaData 
     *
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not processable by this replacement
     *         strategy.
     */
/*   abstract protected static function checkMetaData( ezcCacheStackMetaData $metaData ) */
}

?>
