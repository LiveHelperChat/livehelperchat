<?php
/**
 * File containing the ezcWebdavNotTransportHandlerException class
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown, when no {@link ezcWebdavTransport} could be found for the
 * requesting client.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavNotTransportHandlerException extends ezcWebdavException
{
    /**
     * Initializes the exception with the given $client and sets the exception
     * message from it.
     * 
     * @param string $client
     */
    public function __construct( $client )
    {
        parent::__construct( "Could not find any ezcWebdavTransport for the client '{$client}'." );
    }
}

?>
