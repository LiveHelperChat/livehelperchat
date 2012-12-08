<?php
/**
 * File containing the ezcWebdavLockOptionsRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the OPTIONS request.
 *
 * This class provides plugin callbacks for the OPTIONS request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockOptionsRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * If this handler requires the backend to get locked. 
     * 
     * @var bool
     */
    public $needsBackendLock = false;

    /**
     * Handles OPTIONS requests.
     *
     * Dummy method to satisfy the interface. Only responses to the OPTIONS
     * request must be handled, which happens in {@link generatedResponse()}.
     *
     * @param ezcWebdavRequest $request  ezcWebdavOptionsRequest
     * @return null
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        return null;
    }

    /**
     * Handles responses to the OPTIONS request.
     *
     * This method enhances the generated response to indicate WebDAV
     * compliance classes 1 and 2 and adds the methods LOCK and UNLOCK to the
     * Allow header.
     *
     * @param ezcWebdavResponse $response 
     * @return ezcWebdavResponse|null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        if ( $response instanceof ezcWebdavOptionsResponse )
        {
            $response->setHeader(
                'DAV',
                ezcWebdavOptionsResponse::VERSION_ONE . ',' . ezcWebdavOptionsResponse::VERSION_TWO 
            );
            $allowHeader = $response->getHeader( 'Allow' ) . ', LOCK, UNLOCK';
            $response->setHeader( 'Allow', $allowHeader );
        }
    }
}

?>
