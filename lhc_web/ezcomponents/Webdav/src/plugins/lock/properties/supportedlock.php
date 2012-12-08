<?php
/**
 * File containing the ezcWebdavSupportedLockProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * An object of this class represents the Webdav property <supportedlock>.
 *
 * @property array(ezcWebdavSupportedLockPropertyLockentry) $lockEntry
 *           Lock information according to <lockentry> elements.
 *
 * @version 1.1.4
 * @package Webdav
 *
 * @access private
 */
class ezcWebdavSupportedLockProperty extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavSourceProperty.
     *
     * The $lockEntry parameter must be an array of {@link
     * ezcWebdavSupportedLockPropertyLockentry} instances.
     * 
     * @param ArrayObject(ezcWebdavSupportedLockPropertyLockentry) $lockEntries
     * @return void
     */
    public function __construct( ArrayObject $lockEntries = null )
    {
        parent::__construct( 'supportedlock' );

        $this->lockEntries = ( $lockEntries === null ? new ArrayObject() : $lockEntries );
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
            case 'lockEntries':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ArrayObject ) )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ArrayObject(ezcWebdavSupportedLockPropertyLockentry)' );
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
        return count( $this->properties['lockEntries'] ) === 0;
    }

    /**
     * Remove all contents from a property.
     *
     * Clear a property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function clear()
    {
        $this->properties['lockEntries'] = new ArrayObject();
    }
}

?>
