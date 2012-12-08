<?php
/**
 * File containing the ezcWebdavMakeCollectionRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Abstract representation of a MKCOL request.
 *
 * An instance of this class represents the WebDAV MKCOL request.
 *
 * @property string $body
 *           The request body of a MKCOL request.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavMakeCollectionRequest extends ezcWebdavRequest
{
    /**
     * Creates a new MKCOL request object.
     *
     * The request contains of the $requestUri which indicates where a
     * collection should be created and an optional request $body.
     * 
     * @param string $requestUri
     * @param string $body
     * @return void
     */
    public function __construct( $requestUri, $body = null )
    {
        // Set from constructor values
        parent::__construct( $requestUri );

        // Create properties
        $this->properties['body'] = $body;
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
