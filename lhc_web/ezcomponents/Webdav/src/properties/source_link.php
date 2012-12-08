<?php
/**
 * File containing the ezcWebdavSourcePropertyLink class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents the Webdav property <source>.
 *
 * @property string $src
 *           Source of the link (URI).
 * @property string $dst
 *           Destination of the link (URI).
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavSourcePropertyLink extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavSourceProperty.
     *
     * The $src parameter is the requested path, while the $dst parameter
     * indicate the source URI.
     * 
     * @param string $src Link source (URI).
     * @param string $dst Link destination (URI).
     * @return void
     */
    public function __construct( $src = null, $dst = null )
    {
        parent::__construct( 'link' );

        $this->src = $src;
        $this->dst = $dst;
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
            case 'src':
                if ( !is_string( $propertyValue ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'string' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            case 'dst':
                if ( !is_string( $propertyValue ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'string' );
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
        return ( ( $this->properties['src'] === null ) || ( $this->properties['dst'] === null ) );
    }
}

?>
