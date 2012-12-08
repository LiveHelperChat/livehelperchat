<?php
/**
 * File containing the ezcCacheStackMetaDataStorage interface.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Interface that must be implemented by stack meta data storages.
 *
 * If a storage is capable to store meta data used by an {@link ezcCacheStack},
 * it must implement this interface. Beside the store and restore methods for
 * the meta data itself, it must implement methods to lock and unlock the
 * complete storage, to ensure that meta data is kept consistent and not
 * affected by race conditions of concurring requests.
 * 
 * @package Cache
 * @version 1.5
 */
interface ezcCacheStackMetaDataStorage
{
    /**
     * Restores and returns the meta data struct.
     *
     * This method fetches the meta data stored in the storage and returns the
     * according object implementing {@link ezcCacheStackMetaData}, that was
     * stored using {@link storeMetaData()}. The meta data must be stored
     * inside the storage, but should not be visible as normal cache items to
     * the user. If no meta data is found, null must be returned.
     * 
     * @return ezcCacheStackMetaData|null
     */
    public function restoreMetaData();

    /**
     * Stores the given meta data struct.
     *
     * This method stores the given $metaData inside the storage. The data must
     * be stored with the same mechanism that the storage itself uses. However,
     * it should not be stored as a normal cache item, if possible, to avoid
     * accedental user manipulation. The class of $metaData must be stored in
     * addition, to reconstruct the correct {@link ezcCacheStackMetaData}
     * implementing class on {@link restoreMetaData}.
     * 
     * @param ezcCacheStackMetaData $metaData 
     */
    public function storeMetaData( ezcCacheStackMetaData $metaData );

    /**
     * Acquire a lock on the storage.
     *
     * This method acquires a lock on the storage. If locked, the storage must
     * block all other method calls until the lock is freed again using {@link
     * ezcCacheStackMetaDataStorage::unlock()}. Methods that are called within
     * the request that successfully acquired the lock must succeed as usual.
     * 
     * @return void
     */
    public function lock();

    /**
     * Release a lock on the storage.
     *
     * This method releases the lock of the storage, that has been acquired via
     * {@link ezcCacheStackMetaDataStorage::lock()}. After this method has been
     * called, blocked method calls (including calls to lock()) can suceed
     * again.
     * 
     * @return void
     */
    public function unlock();
}

?>
