<?php
/**
 * File containing the ezcWebdavSourceProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents the Webdav property <source>.
 *
 * @property array(ezcWebdavSourcePropertyLink) $links
 *           Lock information according to <link> elements.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavSourceProperty extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavSourceProperty.
     *
     * The given array must contain instances of {@link
     * ezcWebdavSourcePropertyLink}.
     * 
     * @param array(ezcWebdavSourcePropertyLink) $links
     * @return void
     */
    public function __construct( array $links = array() )
    {
        parent::__construct( 'source' );

        $this->links = $links;
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
            case 'links':
                if ( !is_array( $propertyValue ) )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'array(ezcWebdavSourcePropertyLink)' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }

    /**
     * Remove all contents from a property.
     *
     * Clear the property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function hasNoContent()
    {
        return !count( $this->properties['links'] );
    }

    /**
     * Removes all contents from a property.
     *
     * Clear the property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function clear()
    {
        parent::clear();

        $this->properties['links'] = array();
    }
}

?>
