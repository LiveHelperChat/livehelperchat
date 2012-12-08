<?php
/**
 * File containing the ezcWebdavLockPutRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the PUT request.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockPutRequestResponseHandler extends ezcWebdavLockMakeCollectionRequestResponseHandler
{
    /**
     * Handles responses to the PUT request.
     *
     *
     * @param ezcWebdavResponse $response 
     * @return ezcWebdavResponse|null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        if ( !( $response instanceof ezcWebdavPutResponse ) )
        {
            return null;
        }
        
        $this->updateLockProperties();
    }
}

?>
