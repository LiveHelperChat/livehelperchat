<?php
/**
 * File containing the ezcWebdavInvalidHeaderException class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when a request/response object receives an invalid header value.
 *
 * {@link ezcWebdavRequest::validateHeaders()} will throw this exception, if a
 * header, which is essential to the specific request, is not present. {@link
 * ezcWebdavTransport::sendResponse()} will throw this exception if a
 * Content-Type or Content-Length header is set in an empty body request.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavInvalidHeaderException extends ezcWebdavBadRequestException
{
    /**
     * Initializes the exception with the given $headerName, $value (the value
     * of the named header) and $expectedValue and sets the exception message
     * from it.
     * 
     * @param string $headerName    Name of the affected header.
     * @param string $value         Contained value.
     * @param string $expectedValue Expected values.
     * @return void
     */
    public function __construct( $headerName, $value, $expectedValue = null )
    {
        $msg = "The value '{$value}' for the header '{$headerName}' is invalid.";
        if ( $expectedValue !== null )
        {
            $msg .= " Allowed values are: " . $expectedValue . '.';
        }
        parent::__construct( $msg );
    }
}



?>
