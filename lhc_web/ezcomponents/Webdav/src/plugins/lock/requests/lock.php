<?php
/**
 * File containing the ezcWebdavLockRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Abstract representation of a LOCK request.
 *
 * An instance of this class represents the WebDAV LOCK request.
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
 * - Depth (default: ezcWebdavRequest::DEPTH_INFINITY)
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @property bool $allProp
 *           Representing the <allprop /> XML element.
 * @property bool $propName 
 *           Representing the <propname /> XML element. 
 * @property array(string) $prop
 *           Representing the <prop /> XML element. Can contain a list of
 *           property names.
 *
 * @access private
 */
class ezcWebdavLockRequest extends ezcWebdavRequest
{
    /**
     * Indicates a read lock. Not supported by RFC 2518.
     */
    const TYPE_READ       = 'read';
    /**
     * Indicates a write lock. Represents the XML element <write /> inside
     * other XML elements. 
     */
    const TYPE_WRITE      = 'write';
                       
    /**
     * Represents a shared lock. 
     */
    const SCOPE_SHARED    = 'shared';
    /**
     * Represents an exclusive lock. 
     */
    const SCOPE_EXCLUSIVE = 'exclusive';

    /**
     * Creates a new LOCK request object.
     * Sets the defaults for the optional headers for this request. Expects the
     * URI of the request to be encapsulated.
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
        $this->headers['Timeout']   = null;

        // Create properties
        $this->properties['lockInfo']  = null;
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
            case 'lockInfo':
                if ( !( $propertyValue instanceof ezcWebdavRequestLockInfoContent ) && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavRequestLockInfoContent or null' );
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
            && $this->headers['Depth'] !== ezcWebdavRequest::DEPTH_INFINITY )
        {

            throw new ezcWebdavInvalidHeaderException(
                'Depth',
                $this->headers['Depth'],
                'ezcWebdavRequest::DEPTH_ZERO or ezcWebdavRequest::DEPTH_INFINITY'
            );
        }

        // Validate common HTTP/WebDAV headers
        parent::validateHeaders();
    }
}

?>
