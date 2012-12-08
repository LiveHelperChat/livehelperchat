<?php
/**
 * File containing the ezcWebdavRequestPropertyBehaviourContent class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class representing the <propertybehaviour /> XML element in the COPY/MOVE request body.
 *
 * An instance of this class represents the <propertybehaviour /> XML element,
 * that may optionally be contained in the body of a COPY or MOVE request.
 * Either of the properties $keepAlive or $omit may be set, but not both of
 * them at once.
 *
 * The $keepAlive property may contain an array of URIs, indicating the
 * properties that must be kept alive during the operation, the constant value
 * ezcWebdavRequestPropertyBehaviourContent::ALL to indicate that all
 * properties must be processed live or null, if the $omit property is set to
 * true. Otherwise an ezcBaseValueException will be thrown.
 *
 * The $omit property may contain either true or false and must be false if the
 * $keepAlive property is used. Otherwise an ezcBaseValueException will be
 * thrown.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @property array|int $keepAlive
 *           Represents the <keepalive /> XML element.
 * @property bool $omit
 *           Represents the <omit /> XML element.
 */
class ezcWebdavRequestPropertyBehaviourContent extends ezcWebdavInfrastructureBase
{
    /**
     * Indicates that the <keepalive /> XML element contained #PCDATA *.
     */
    const ALL = 0;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->properties['keepAlive'] = null;
        $this->properties['omit']      = false;
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
            case 'keepAlive':
                if ( !is_array( $propertyValue ) && $propertyValue !== self::ALL && $propertyValue !== null )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'array(string), self::ALL or null'
                    );
                }
                if ( $propertyValue !== null && $this->omit === true )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'property $omit must be false'
                    );
                }
                break;
            case 'omit':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                if ( $propertyValue !== null && $this->keepAlive !== null )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'property $keepAlive must be null'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
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
}

?>
