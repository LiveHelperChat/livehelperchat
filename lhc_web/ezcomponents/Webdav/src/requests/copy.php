<?php
/**
 * File containing the ezcWebdavCopyRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract representation of a COPY request.
 *
 * An instance of this class represents the WebDAV COPY request.
 *
 * Required headers for this request are:
 * <ul>
 * <li>Destination</li>
 * </ul>
 *
 * Optional headers for this request are:
 * <ul>
 * <li>Overwrite (default: 'T')</li>
 * <li>Depth (default: ezcWebdavRequest::DEPTH_ZERO)</li>
 * </ul>
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @property ezcWebdavRequestPropertyBehaviourContent $propertyBehaviour
 *           Contains the <propertybehavior /> element, if submitted with the
 *           request. If not, this property is null.
 */
class ezcWebdavCopyRequest extends ezcWebdavRequest
{
    /**
     * Creates a new COPY request object.
     *
     * Expected are the $requestUri, which references the source to copy, and
     * the $destination, which is the URI referencing the destination resource
     * to copy to.
     * 
     * @param string $requestUri
     * @param string $destination
     * @return void
     */
    public function __construct( $requestUri, $destination )
    {
        // Set from constructor values
        parent::__construct( $requestUri );

        $this->headers['Destination'] = $destination;

        // Set header defaults
        $this->headers['Overwrite'] = 'T';
        $this->headers['Depth']     = ezcWebdavRequest::DEPTH_ZERO;

        // Create properties
        $this->properties['propertyBehaviour'] = null;
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
            case 'propertyBehaviour':
                if ( !( $propertyValue instanceof ezcWebdavRequestPropertyBehaviourContent ) && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavRequestPropertyBehaviourContent' );
                }
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Validates the headers set in this request.
     *
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
        if ( !isset( $this->headers['Destination'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Destination' );
        }

        if ( !isset( $this->headers['Overwrite'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Overwrite' );
        }

        if ( $this->headers['Overwrite'] !== 'T' && $this->headers['Overwrite'] !== 'F' )
        {
            throw new ezcWebdavInvalidHeaderException(
                'Overwrite',
                $this->headers['Overwrite'],
                "'T' or 'F'"
            );
        }
        
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
