<?php
/**
 * File containing the ezcWebdavLockAuthorizer interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface to be implemented by authorization classes for the lock plugin.
 *
 * The lock plugin requires an authorization and authentication object to be
 * used in the server, which implements this interface.
 * 
 * @package Webdav
 * @version 1.1.4
 */
interface ezcWebdavLockAuthorizer extends ezcWebdavAuthorizer
{
    /**
     * Assign a $lockToken to a given $user.
     *
     * The authorization backend needs to save an arbitrary number of lock
     * tokens per user. A lock token is a of maximum length 255
     * containing:
     *
     * <ul>
     *  <li>characters</li>
     *  <li>numbers</li>
     *  <li>dashes (-)</li>
     * </ul>
     * 
     * @param string $user 
     * @param string $lockToken 
     * @return void
     */
    public function assignLock( $user, $lockToken );

    /**
     * Returns if the given $lockToken is owned by the given $user.
     *
     * Returns true, if the $lockToken is owned by $user, false otherwise.
     * 
     * @param string $user 
     * @param string $lockToken 
     * @return bool
     */
    public function ownsLock( $user, $lockToken );
    
    /**
     * Removes the assignement of $lockToken from $user.
     *
     * After a $lockToken has been released from the $user, the {@link
     * ownsLock()} method must return false for the given combination. It might
     * happen, that a lock is to be released, which already has been removed.
     * This case must be ignored by the method.
     * 
     * @param string $user 
     * @param string $lockToken 
     */
    public function releaseLock( $user, $lockToken );
}

?>
