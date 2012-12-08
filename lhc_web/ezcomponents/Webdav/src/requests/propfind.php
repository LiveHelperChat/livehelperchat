<?php
/**
 * File containing the ezcWebdavPropFindRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Abstract representation of a PROPFIND request.
 *
 * An instance of this class represents the WebDAV PROPFIND request.
 *
 * An object of this class may have 1 of 3 possible properties used. If none is
 * used, it must be asumed that the {@link $allProp} property is set to true.
 *
 * The {@link $allProp} property indicates, that a list of all available
 * properties is required, including the value of each property.
 * In contrast, the {@link $propName} property indicates, that only a list of
 * property names, without property values, is requested. The {@link $prop}
 * property is of type array (or null, if not set) and can contain a list of
 * property names, which are requested to be returned, including their values.
 *
 * Optional headers for this request are:
 * <ul>
 * <li>Depth (default: ezcWebdavRequest::DEPTH_INFINITY)</li>
 * </ul>
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @property bool $allProp
 *           Representing the <allprop /> XML element.
 * @property bool $propName 
 *           Representing the <propname /> XML element. 
 * @property array(string)|null $prop
 *           Representing the <prop /> XML element. Can contain a list of
 *           property names.
 */
class ezcWebdavPropFindRequest extends ezcWebdavRequest
{
    /**
     * Creates a new PROPFIND request object.
     *
     * Sets the defaults for the optional headers for this request. The
     * $requestUri idenitifie the resource for which properties will be
     * searched.
     * 
     * @param string $requestUri
     * @return void
     */
    public function __construct( $requestUri )
    {
        // Set from constructor values
        parent::__construct( $requestUri );

        // Set header defaults
        $this->headers['Depth']     = ezcWebdavRequest::DEPTH_INFINITY;

        // Create properties
        $this->properties['allProp']  = false;
        $this->properties['propName'] = false;
        $this->properties['prop']     = null;
    }

    /**
     * Sets a property.
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
            case 'allProp':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                if ( $propertyValue === true && ( $this->propName === true || $this->prop !== null ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, '$propName and $prop must be null' );
                }
                break;
            case 'propName':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                if ( $propertyValue === true && ( $this->allProp !== false || $this->prop !== null ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, '$allProp must be false and $prop must be null' );
                }
                break;
            case 'prop':
                if ( !( $propertyValue instanceof ezcWebdavPropertyStorage ) && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavPropertyStorage' );
                }
                if ( $propertyValue !== null && ( $this->allProp !== false || $this->propName === true ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, '$allProp must be false and $propName must be null' );
                }
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Validates the headers set in this request.
     * This method validates that all required headers are available and that
     * all feasible headers for this request have valid values.
     *
     * @return void
     *
     * @throws ezcWebdavMissingHeaderException
     *         if a required header is missing.
     * @throws ezcWebdavInvalidHeaderException
     *         if a header is present, but its content does not validate.
     */
    public function validateHeaders()
    {
        if ( !isset( $this->headers['Depth'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Depth' );
        }

        if ( $this->headers['Depth'] !== ezcWebdavRequest::DEPTH_ZERO
            && $this->headers['Depth'] !== ezcWebdavRequest::DEPTH_ONE 
            && $this->headers['Depth'] !== ezcWebdavRequest::DEPTH_INFINITY )
        {

            throw new ezcWebdavInvalidHeaderException(
                'Depth',
                $this->headers['Depth'],
                'ezcWebdavRequest::DEPTH_ZERO, ezcWebdavRequest::DEPTH_ONE or ezcWebdavRequest::DEPTH_INFINITY'
            );
        }

        // Validate common HTTP/WebDAV headers
        parent::validateHeaders();
    }
}

?>
