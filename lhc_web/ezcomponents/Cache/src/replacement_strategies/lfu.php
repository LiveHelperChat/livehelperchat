<?php
/**
 * File containing the ezcCacheStackLfuReplacementStrategy.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Least frequently used replacement strategy.
 *
 * This replacement strategy will purge items first that have been used least
 * frequently. In case the {@link ezcCacheStackableStorage} this replacement
 * strategy works on runs full, first all outdated items (which are older than
 * TTL) will be purged. If this does not last to achieve the desired free rate,
 * items will be purged that have been stored or restored least often.
 *
 * This class is not intended to be used directly, but should be configured to
 * be used by an {@link ezcCacheStack} instance. This can be achieved via
 * {@link ezcCacheStackOptions}. The meta data class used by this class is
 * {@link ezcCacheStackLfuMetaData}.
 *
 * For more information on LFU see {@see
 * http://en.wikipedia.org/wiki/Cache_algorithms}.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackLfuReplacementStrategy extends ezcCacheStackBaseReplacementStrategy
{
    /**
     * Stores the given $itemData in the given storage.
     *
     * This method stores the given $itemData under the given $itemId and
     * assigns the given $itemAttributes to it in the {@link
     * ezcCacheStackableStorage} configured in $conf. The
     * storing results in an update of $metaData, reflecting that the item with
     * $itemId was used by raising its use frequence.
     *
     * In case the number of items in the storage exceeds $conf->itemLimit,
     * items will be deleted from the storage. First all outdated items will be
     * removed using {@link ezcCacheStackableStorage::purge()}. If this does
     * not free the desired $conf->freeRate fraction of $conf->itemLimit, those
     * items that have been used least frequently will be deleted. The changes of
     * freeing items are recorded in $metaData.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param mixed $itemData
     * @param array(string=>string) $itemAttributes
     *
     * @see ezcCacheStackReplacementStrategy::store()
     *
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not an instance of {@link
     *         ezcCacheStackLfuMetaData}.
     */
    public static function store(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemData,
        $itemAttributes = array()
    )
    {
        self::checkMetaData( $metaData );
        return parent::store( $conf, $metaData, $itemId, $itemData, $itemAttributes );
    }

    /**
     * Restores the data with the given $itemId from the storage configured in $conf.
     *
     * This method restores the item data identified by $itemId and optionally
     * $itemAttributes from the {@link ezcCacheStackableStorage} given in
     * $conf using {@link ezcCacheStackableStorage::restore()}. The result of
     * this action is returned by the method. This means, the desired item data
     * is returned on success, false is returned if the data is not available.
     *
     * A successful restore is recorded in $metaData as a "recent usage", with
     * incrementing the usage counter of $itemId by 1. A restore failure
     * results in a removal of $itemId.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param array(string=>string) $itemAttributes
     * @param bool $search
     *
     * @see ezcCacheStackReplacementStrategy::restore()
     *
     * @return mixed Restored data or false.
     *
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not an instance of {@link
     *         ezcCacheStackLfuMetaData}.
     */
    public static function restore(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemAttributes = array(),
        $search = false
    )
    {
        self::checkMetaData( $metaData );
        return parent::restore( $conf, $metaData, $itemId, $itemAttributes, $search );
    }

    /**
     * Deletes the data with the given $itemId from the given $storage.
     *
     * Deletes the desired item with $itemId and optionally $itemAttributes
     * from the {@link ezcCacheStackableStorage} configured in $conf using. The
     * item IDs returned by this call are updated in $metaData, that they are
     * no longer stored in the $storage.
     *
     * @param ezcCacheStackStorageConfiguration $conf
     * @param ezcCacheStackMetaData $metaData
     * @param string $itemId
     * @param array(string=>string) $itemAttributes
     * @param bool $search
     *
     * @see ezcCacheStackReplacementStrategy::delete()
     *
     * @return array(string) Deleted item IDs.
     *
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not an instance of {@link
     *         ezcCacheStackLfuMetaData}.
     */
    public static function delete(
        ezcCacheStackStorageConfiguration $conf,
        ezcCacheStackMetaData $metaData,
        $itemId,
        $itemAttributes = array(),
        $search = false
    )
    {
        self::checkMetaData( $metaData );
        return parent::delete( $conf, $metaData, $itemId, $itemAttributes, $search );
    }

    /**
     * Returns a fresh meta data instance.
     *
     * Returns a freshly created instance of {@link ezcCacheStackLfuMetaData}.
     * 
     * @return ezcCacheStackLfuMetaData
     */
    public static function createMetaData()
    {
        return new ezcCacheStackLfuMetaData();
    }

    /**
     * Checks if the given meta data is processable.
     *
     * Throws an exception if the given meta data is not processable.
     * 
     * @param ezcCacheStackMetaData $metaData 
     *
     * @throws ezcCacheInvalidMetaDataException
     *         if the given $metaData is not an instance of {@link
     *         ezcCacheStackLfuMetaData}.
     *
     * @access private
     */
    protected static function checkMetaData( ezcCacheStackMetaData $metaData )
    {
        if ( !( $metaData instanceof ezcCacheStackLfuMetaData ) )
        {
            throw new ezcCacheInvalidMetaDataException(
                $metaData,
                'ezcCacheStackLfuMetaData'
            );
        }
    }
}

?>
