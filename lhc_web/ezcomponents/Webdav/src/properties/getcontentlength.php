<?php
/**
 * File containing the ezcWebdavGetContentLengthProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents the Webdav property <getcontentlength>.
 *
 * @property string $length
 *           The content length.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavGetContentLengthProperty extends ezcWebdavLiveProperty
{
    /**
     * The WebDav RFC defines that each DAV: compliant resource must have this
     * property set. It does not define what should be returned for
     * collections. We use the string in this constant for this.
     */
    const COLLECTION = '4096';

    /**
     * Creates a new ezcWebdavGetContentLengthProperty.
     * 
     * Creates a new ezcWebdavGetContentLengthProperty with the given length.
     * The length should be given as a string to avoid integer overflows.
     *
     * @param string $length The length.
     * @return void
     */
    public function __construct( $length = null )
    {
        parent::__construct( 'getcontentlength' );

        $this->length = $length;
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
            case 'length':
                if ( ( !is_string( $propertyValue ) || !is_numeric( $propertyValue ) ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'string of digits' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }

    /**
     * Returns if property has no content.
     *
     * Returns true, if the property has no content stored.
     * 
     * @return bool
     */
    public function hasNoContent()
    {
        return $this->properties['length'] === null;
    }
}

?>
