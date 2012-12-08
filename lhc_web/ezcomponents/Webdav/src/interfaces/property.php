<?php
/**
 * File containing the abstract ezcWebdavProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class for WebDAV property representation classes.
 * 
 * @property string $namespace
 *           Namespace of property
 * @property string $name
 *           Name of property
 * @property-read bool $hasError
 *           If the property has a property validation error.
 * @property-read array $errors
 *           Validation errors for property
 *
 * @package Webdav
 * @version 1.1.4
 */
abstract class ezcWebdavProperty extends ezcWebdavInfrastructureBase
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties;
    
    /**
     * Creates a new property object.
     *
     * Creates a new property by namespace and name.
     * 
     * @param string $namespace
     * @param string $name
     * @return void
     */
    public function __construct( $namespace, $name )
    {
        $this->properties['hasError'] = false;
        $this->properties['errors']   = array();

        $this->namespace = $namespace;
        $this->name      = $name;
    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
    
    /**
     * Indicates that a property has a validation error.
     *
     * Method called, when a property validation error occurs. The error is
     * stored and the property is set as errnous.
     * 
     * @param string $property 
     * @param mixed $value 
     * @param string $expected 
     * @return void
     */
    protected function hasError( $property, $value, $expected )
    {
        $this->properties['hasError'] = true;
        $this->properties['errors'][] = "The property '$property' only accepts values of type '$expected' - '" . gettype( $value ) . "' given.";
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
            case 'name':
            case 'namespace':
                // if ( !is_string( $propertyValue ) || strlen( $propertyValue ) < 1 )
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string, length > 0' );
                }
                break;

            case 'errors':
            case 'hasError':
                throw new ezcBasePropertyPermissionException(
                    $propertyName,
                    ezcBasePropertyPermissionException::READ
                );
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Returns if a property exists.
     *
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }

    /**
     * Check if property has no content.
     *
     * The implementation of this method must return true, if no content is
     * stored in the property.
     * 
     * @return bool
     */
    abstract public function hasNoContent();

    /**
     * Remove all contents from a property.
     *
     * Clear a property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function clear()
    {
        foreach ( $this->properties as $name => $value )
        {
            if ( !in_array( $name, array( 'name', 'namespace', 'errors', 'hasError' ), true ) )
            {
                $this->properties[$name] = null;
            }
        }
    }

    /**
     * Clones a property deeply.
     * 
     * @return void
     */
    public function __clone()
    {
        foreach ( $this->properties as $name => $val )
        {
            if ( is_object( $val ) )
            {
                $this->properties[$name] = clone $val;
            }
        }
    }
}

?>
