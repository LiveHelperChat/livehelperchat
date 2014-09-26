<?php
/**
 * File containing the abstract ezcCacheStackBaseMetaData class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Common base class for meta data.
 * 
 * Common base class for
 * <ul>
 *  <li>{@link ezcCacheStackLruMetaData}</li>
 *  <li>{@link ezcCacheStackLfuMetaData}</li>
 * </ul>
 *
 * Private to not expose internal APIs, yet. Might become public in future
 * releases.
 *
 * @package Cache
 * @version 1.5
 *
 * @access private
 */
abstract class ezcCacheStackBaseMetaData implements ezcCacheStackMetaData
{
    /**
     * Replacement data.
     *
     * <code>
     * array(
     *      '<item_id>' => <replacement_value>,
     *      '<item_id>' => <replacement_value>,
     *      '<item_id>' => <replacement_value>,
     *      // ...
     * )
     * </code>
     * 
     * @var array(string=>int)
     */
    protected $replacementData = array();

    /**
     * Storage-item assignement.
     *
     * <code>
     * array(
     *      '<storage_id>' => array(
     *          '<item_id>' => true,
     *          '<item_id>' => true,
     *          '<item_id>' => true,
     *          // ...
     *      ),
     *      '<storage_id>' => array(
     *          '<item_id>' => true,
     *          '<item_id>' => true,
     *          '<item_id>' => true,
     *          // ...
     *      ),
     *      // ...
     * )
     * </code>
     * 
     * @var array(string=>array(string=>bool))
     */
    protected $storageData = array();

    /**
     * No-parameter constructor.
     * 
     */
    public function __construct()
    {
    }
   
    /**
     * Adds the given $itemId to the storage identified by $storageId.
     *
     * This method adds the given $itemId to the storage identified by
     * $storageId. It must also add the $itemId to the replacement data or
     * update its status there, if the item is already in there.
     *
     * The method calls the abstract {@link self::addItemToReplacementData()}
     * method to make the item be reflected in the replacement data.
     * 
     * @param string $storageId 
     * @param string $itemId 
     */
    public function addItem( $storageId, $itemId )
    {
        $this->storageData[$storageId][$itemId] = true;
        // Abstract call
        $this->addItemToReplacementData( $itemId );
    }

    /**
     * Adds the given $itemId to the replacement data.
     *
     * This method is called by {@link self::addItem()} in order to add the
     * given $itemId to the replacement data in the correct way. When this
     * method is called, the item has already been added to the storage data.
     * The removal from the replacement data happens automatically in {@link
     * self::removeItem()} by unsetting the key $itemId in
     * {self::$replacementData}.
     *
     * If the entry for $itemId already exists, it must be updated to reflect a
     * touching of the data (store/restore).
     * 
     * @param string $itemId 
     */
    abstract public function addItemToReplacementData( $itemId );

    /**
     * Removes the given $itemId from the storage identified by $storageId.
     * 
     * Removes the given $itemId from the storage identified by $storageId in
     * {@link self::$storageData} and also removes the key from {@link
     * self::$metaData}.
     *
     * @param string $storageId 
     * @param string $itemId 
     */
    public function removeItem( $storageId, $itemId )
    {
        // Remove from storage
        if ( isset( $this->storageData[$storageId] ) )
        {
            // No error if not set
            unset( $this->storageData[$storageId][$itemId] );
            // Remove empty storage
            if ( count( $this->storageData[$storageId] ) === 0 )
            {
                unset( $this->storageData[$storageId] );
            }
        }

        // Check for complete removal
        foreach ( $this->storageData as $storage => $items )
        {
            if ( isset( $items[$itemId] ) )
            {
                // Item is available in other storage
                return;
            }
        }
        // No storage has the item, savely unset it
        unset( $this->replacementData[$itemId] );
    }

    /**
     * Returns if the given $itemId is available in the storage with $storageId.
     *
     * Returns if the $itemId is availalke in the storage that is identified by
     * $storageId.
     * 
     * @param string $storageId 
     * @param string $itemId 
     * @return bool
     */
    public function hasItem( $storageId, $itemId )
    {
        return isset( $this->storageData[$storageId][$itemId] );
    }

    /**
     * Returns if the given $storageId has reached the given $limit of items.
     *
     * Returns if the storage identified by $storageId has $limit or more items
     * stored.
     * 
     * @param string $storageId 
     * @param int $limit 
     * @return bool
     */
    public function reachedItemLimit( $storageId, $limit )
    {
        return (
            isset( $this->storageData[$storageId] )
            && count( $this->storageData[$storageId] ) >= $limit
        );
    }

    /**
     * Returns an array of item IDs ordered in replacement order.
     *
     * This method sorts {@link asort()} the {@link self::$replacementData} and
     * returns it. The data is ordered by the replacement value in ascending
     * order.
     * 
     * @return array(string=>int)
     */
    public function getReplacementItems()
    {
        asort( $this->replacementData );
        return $this->replacementData;
    }

    /**
     * Returns the data data as an array.
     * 
     * @return array
     */
    public function getState()
    {
        return array(
            'replacementData' => $this->replacementData,
            'storageData'     => $this->storageData,
        );
    }

    /**
     * Sets the intrnal data from the given array $data.
     *
     * The $data array was previously generated by {@link self::getState()}.
     * 
     * @param array $data 
     */
    public function setState( array $data )
    {
        $this->replacementData = $data['replacementData'];
        $this->storageData     = $data['storageData'];
    }
}

?>
