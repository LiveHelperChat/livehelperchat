<?php
/**
 * File containing the ezcWebdavLockMultipleCheckObserver class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Check observer to couple different observers.
 *
 * This observer combines multiple observers to a singe one.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockMultipleCheckObserver implements ezcWebdavLockCheckObserver
{
    /**
     * Observers contained in this observer.
     * 
     * @var array(ezcWebdavLockCheckObserver)
     */
    protected $observers;

    /**
     * Attach an observer.
     * 
     * @param ezcWebdavLockCheckObserver $observer 
     * @return void
     */
    public function attach( ezcWebdavLockCheckObserver $observer )
    {
        $this->observers[] = $observer;
    }

    /**
     * Returns all observers contained in this observer. 
     * 
     * @return array(ezcWebdavLockCheckObserver)
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Notifies all contained observers.
     * 
     * @param ezcWebdavPropFindResponse $response 
     * @return void
     */
    public function notify( ezcWebdavPropFindResponse $response )
    {
        foreach ( $this->observers as $observer )
        {
            $observer->notify( $response );
        }
    }
}

?>
