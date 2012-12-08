<?php
/**
 * File containing the ezcPersistentSessionIdentityDecoratorOptions class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Options class for ezcPersistentSessionIdentityDecorator.
 *
 * @property bool $refetch
 *                If this option is set to true, the identity session will
 *                re-fetch all objects when they are requested, instead of
 *                getting them from the identity map. Attention: This might
 *                lead to inconsistencies, if you are using old object
 *                instances in your application!
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentSessionIdentityDecoratorOptions extends ezcBaseOptions
{
    /**
     * Construct a new options object.
     *
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
        $this->properties['refetch'] = false;
        parent::__construct( $options );
    }

    /**
     * Sets an option.
     *
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
            case 'refetch':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                break;
            
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }

        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
