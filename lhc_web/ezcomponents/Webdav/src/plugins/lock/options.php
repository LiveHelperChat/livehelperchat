<?php
/**
 * File containing the ezcWebdavLockPluginOptions class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Option class for the Webdav lock plugin.
 *
 * You can use an object of this class, to set options for the lock plugin in
 * {@link ezcWebdavLockPluginConfiguration}.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavLockPluginOptions extends ezcBaseOptions
{
    /**
     * Construct a new options object.
     * Options are constructed from an option array by default. The constructor
     * automatically passes the given options to the __set() method to set them 
     * in the class.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If trying to access a non existent property.
     * @throws ezcBaseValueException
     *         If the value for a property is out of range.
     * @param array(string=>mixed) $options The initial options to set.
     */
    public function __construct( array $options = array() )
    {
        $this->properties['lockTimeout']         = 900;
        $this->properties['backendLockTimeout']  = 10000000;
        $this->properties['backendLockWaitTime'] = 10000;
        parent::__construct( $options );
    }

    /**
     * Sets an option.
     * This method is called when an option is set.
     * 
     * @param string $propertyName  The name of the option to set.
     * @param mixed $propertyValue The option value.
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
            case 'lockTimeout':
                if ( !is_int( $propertyValue ) || $propertyValue < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'int > 0' );
                }
                break;
            case 'backendLockTimeout':
                if ( !is_int( $propertyValue ) || $propertyValue < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'int > 0' );
                }
                break;
            case 'backendLockWaitTime':
                if ( !is_int( $propertyValue ) || $propertyValue < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'int > 0' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }

        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
