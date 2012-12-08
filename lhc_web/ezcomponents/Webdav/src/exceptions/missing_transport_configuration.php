<?php
/**
 * File containing the ezcWebdavMissingTransportConfigurationException class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown if no transport configuration could be found to satisfy a configuration.
 *
 * This exception is thrown by {@link ezcWebdavServerConfigurationManager} if it could
 * not find an {@link ezcWebdavServerConfiguration} that provides a regex to
 * match the given $userAgent.
 *
 * This can only occur if the configuration for the basic RFC compliant {@link
 * ezcWebdavTransport} has been removed, since this one ussually does a
 * catch-all on all clients that have no special extended transport.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavMissingTransportConfigurationException extends ezcWebdavException
{
    /**
     * Initializes the exception with the given $userAgent and sets the exception
     * message from it.
     * 
     * @param string $userAgent Name of the User-Agent header that lead to the exception.
     * @return void
     */
    public function __construct( $userAgent )
    {
        parent::__construct( "There could be no ezcWebdavServerConfiguration be found to satisfy the User-Agent '$userAgent'. Seems like the basic RFC transport has also been removed." );
    }
}

?>
