<?php
/**
 * File containing the ezcWebdavLockCheckObserver interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Interface that needs to be implemented by observers to lock checks.
 *
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
interface ezcWebdavLockCheckObserver
{
    /**
     * Notify about a response.
     *
     * Notifies the observer that a the given $response was checked. The
     * observer should not immediatelly perform any action on this event, but
     * just prepare actions that can be issued by the user at a later time
     * using a dedicated method. This is necessary since a later check might
     * still fail and the prepared actions must not be performed at all.
     * 
     * @param ezcWebdavPropFindResponse $response 
     */
    public function notify( ezcWebdavPropFindResponse $response );
}

?>
