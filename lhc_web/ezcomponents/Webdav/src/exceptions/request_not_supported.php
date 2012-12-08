<?php
/**
 * File containing the ezcWebdavRequestNotSupportedException class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a request object could not be handled by a backend.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavRequestNotSupportedException extends ezcWebdavException
{
    /**
     * Initializes the exception with the given $request and an optional reaon
     * $message and sets the exception message from it.
     * 
     * @param ezcWebdavRequest $request 
     * @param mixed $message 
     * @return void
     */
    public function __construct( ezcWebdavRequest $request, $message = null )
    {
        parent::__construct(
            "The request type '" . get_class( $request ) . "' is not supported by the transport." . ( $message !== null ? ' ' . $message : '' )
        );
    }
}

?>
