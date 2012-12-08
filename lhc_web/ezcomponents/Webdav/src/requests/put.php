<?php
/**
 * File containing the ezcWebdavPutRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Abstract representation of a PUT request.
 *
 * An instance of this class represents the WebDAV PUT request.
 *
 * @property string $body
 *           The request body of a PUT request.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavPutRequest extends ezcWebdavRequest
{
    /**
     * Creates a new PUT request object.
     *
     * The request object indicates, that the given $body should be stored in
     * the resource identified by $requestUri.
     * 
     * @param string $requestUri
     * @param string $body
     * @return void
     */
    public function __construct( $requestUri, $body )
    {
        // Set from constructor values
        parent::__construct( $requestUri );
        
        // Create properties
        $this->properties['body'] = (string) $body;
    }

    /**
     * Validates the headers set in this request.
     *
     * This method validates that all required headers are available and that
     * all feasible headers for this request have valid values.
     *
     * @return void
     *
     * @throws ezcWebdavMissingHeaderException
     *         if a required header is missing.
     * @throws ezcWebdavInvalidHeaderException
     *         if a header is present, but its content does not validate.
     */
    public function validateHeaders()
    {
        if ( !isset( $this->headers['Content-Length'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Content-Length' );
        }
        if ( !isset( $this->headers['Content-Type'] ) )
        {
            $this->setHeader( 'Content-Type', 'application/octet-stream' );
        }

        // Validate common HTTP/WebDAV headers
        parent::validateHeaders();
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'body':
                throw new ezcBasePropertyPermissionException( 
                    $propertyName,
                    ezcBasePropertyPermissionException::READ
                );

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }
}

?>
