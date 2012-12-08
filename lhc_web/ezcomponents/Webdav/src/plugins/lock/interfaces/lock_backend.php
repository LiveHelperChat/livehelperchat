<?php
/**
 * File containing the ezcWebdavLockBackend interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface to be implemented by backends which should be used with the lock plugin.
 *
 * The lock plugin interacts with the backend only be sending {@link
 * ezcWebdavRequest} requests, except for that it requires the backend to
 * implement this interface.
 *
 * The lock plugin will lock the backend as soon as it comes into action and
 * release the lock, when all processing is done. The reason for the lock is to
 * keep communication between the lock plugin and the backend atomic.
 * 
 * @package Webdav
 * @version 1.1.4
 */
interface ezcWebdavLockBackend
{
    /**
     * Acquire a backend lock.
     *
     * This method must acquire an exclusive lock of the backend. If the
     * backend is already locked by a different request, the must must retry to
     * acquire the lock continously and wait between each retry $waitTime micro
     * seconds. If $timeout microseconds have passed since the method was
     * called, it must throw an exception of type {@link
     * ezcWebdavLockTimeoutException}.
     * 
     * @param int $waitTime Microseconds.
     * @param int $timeout Microseconds.
     * @return void
     */
    public function lock( $waitTime, $timeout );

    /**
     * Release the backend lock.
     *
     * This method is called to unlock the backend. The lock that was acquired
     * using {@link lock()} must be released, so that the backend can be locked
     * by another request.
     * 
     * @return void
     */
    public function unlock();
}

?>
