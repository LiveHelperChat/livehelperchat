<?php
/**
 * File containing the ezcWebdavDeadProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents a WebDAV dead property.
 *
 * An object of this class is created to represent a dead (in the meaning of
 * unknown) property. While live properties are validated and maintained by the
 * server itself, while dead properties are just stored without any further
 * action.
 *
 * @property string $content
 *           The content of a dead property.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavDeadProperty extends ezcWebdavProperty
{
    /**
     * Creates a new dead property.
     *
     * Creates a new dead property in the given $namespace with the given $name
     * and optionally containing $content.
     * 
     * @param string $namespace
     * @param string $name
     * @param string $content
     * @return void
     */
    public function __construct( $namespace, $name, $content = null )
    {
        parent::__construct( $namespace, $name );
        $this->content = $content;
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
            case 'content':
                if ( ( $propertyValue !== null ) && !is_string( $propertyValue ) )
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
        return $this->properties['content'] === null;
    }
}

?>
