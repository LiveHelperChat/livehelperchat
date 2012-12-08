<?php
/**
 * File containing the ezcWebdavLockDiscoveryProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * An object of this class represents the Webdav property <lockdiscovery>.
 *
 * @property ArrayObject(ezcWebdavLockDiscoveryPropertyActiveLock) $activeLock
 *           Lock information according to <activelock> elements.
 *
 * @version 1.1.4
 * @package Webdav
 *
 * @access private
 */
class ezcWebdavLockDiscoveryProperty extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavLockDiscoveryProperty.
     *
     * The given array must contain instances of {@link
     * ezcWebdavLockDiscoveryPropertyActiveLock}.
     * 
     * @param ArrayObject(ezcWebdavLockDiscoveryPropertyActiveLock) $activeLock
     */
    public function __construct( ArrayObject $activeLock = null )
    {
        parent::__construct( 'lockdiscovery' );

        $this->activeLock = ( $activeLock === null ? new ArrayObject() : $activeLock );
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
            case 'activeLock':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ArrayObject ) )
                {
                    return $this->hasError(
                        $propertyName,
                        $propertyValue,
                        'ArrayObject(ezcWebdavLockDiscoveryPropertyActiveLock)'
                    );
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
        return count( $this->properties['activeLock'] ) === 0;
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
        $this->activeLock = new ArrayObject();
    }

    /**
     * Clones deep.
     * 
     * @return void
     */
    public function __clone()
    {
        $activeLocks                    = $this->properties['activeLock'];
        $this->properties['activeLock'] = new ArrayObject();
        foreach ( $activeLocks as $activeLock )
        {
            $this->properties['activeLock'][] = clone $activeLock;
        }
    }
}

?>
