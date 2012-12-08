<?php
/**
 * File containing the ezcWebdavMissingHeaderException class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a request/response object misses an essential header essential.
 *
 * {@link ezcWebdavRequest::validateHeaders()} will throw this exception, if a
 * header, which is essential to the specific request, is not present. {@link
 * ezcWebdavTransport::sendResponse()} will throw this exception, if a non-XML
 * body should be sent and the header is not set.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavMissingHeaderException extends ezcWebdavBadRequestException
{
    /**
     * Initializes the exception with the given $headerName and sets the exception
     * message from it.
     * 
     * @param string $headerName Name of the missing header.
     * @return void
     */
    public function __construct( $headerName )
    {
        parent::__construct( "The header '$headerName' is required by the request sent or the response to send but was not set." );
    }
}

?>
