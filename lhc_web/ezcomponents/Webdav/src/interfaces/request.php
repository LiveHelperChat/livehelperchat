<?php
/**
 * File containing the abstract ezcWebdavRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class for request objects.
 *
 * This base class must be extended by all request representation classes.
 *
 * @version 1.1.4
 * @package Webdav
 */
abstract class ezcWebdavRequest
{
    /**
     * Constants for the 'Depth' headers and property fields. 
     */
    const DEPTH_ZERO      =  0;
    const DEPTH_ONE       =  1;
    const DEPTH_INFINITY  = -1;

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();


    /**
     * Container for header information. 
     * 
     * @var array(string=>mixed)
     */
    protected $headers = array();

    /**
     * Indicates wheather the validateHeaders method has already been called.
     *
     * Otherwise getHeader() will throw an exception because of unvalidated
     * headers.
     * 
     * @var bool
     */
    private $validated = false;

    /**
     * Creates a new request object.
     *
     * Creates a new request object that refers to the given $requestUri, which
     * is a path understandable by the {@link ezcWebdavBackend}.
     * 
     * @param string $requestUri 
     * @return void
     */
    public function __construct( $requestUri )
    {
        $this->properties['requestUri'] = $requestUri;
        $this->setHeader( 'Authorization', new ezcWebdavAnonymousAuth() );
    }

    /**
     * Validates the headers set in this request.
     *
     * This method is called by {@link ezcWebdavServer} after the request object has
     * been created by an {@link ezcWebdavTransport}. It must validate all headers
     * specific for this request for existance of required headers and validity
     * of all headers used  by the specific request implementation. The call of
     * the parent method is *mandatory* to have common WebDAV and HTTP headers
     * validated, too!
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
        $this->validated = true;
    }

    /**
     * Sets a header to a specified value.
     *
     * Sets the value for $header to $headerValue. All processable headers will
     * be validated centrally in {@link validateHeaders()}.
     *
     * For validation of header content, the method {@link validateHeaders()}
     * can be overwritten.
     * 
     * @param string $headerName 
     * @param mixed $headerValue 
     * @return void
     */
    public final function setHeader( $headerName, $headerValue )
    {
        $this->headers[$headerName] = $headerValue;
        $this->validated = false;
    }

    /**
     * Sets a header to a specified value.
     *
     * Sets the values for the headers given in $headers to the specified
     * values. All processable headers will be validated centrally in {@link
     * validateHeaders()}.
     *
     * For validation of header content, the method {@link validateHeaders()}
     * can be overwritten.
     * 
     * @param array $headers 
     * @return void
     */
    public final function setHeaders( array $headers )
    {
        $this->headers = array_merge( $this->headers, $headers );
        $this->validated = false;
    }

    /**
     * Returns the contents of a specific header.
     *
     * Returns the content of the header identified with $headerName with the
     * given name and null if no content for the header is available.
     * 
     * @param string $headerName 
     * @return mixed
     */
    public final function getHeader( $headerName )
    {
        if ( $this->validated !== true )
        {
            throw new ezcWebdavHeadersNotValidatedException( $headerName );
        }

        return isset( $this->headers[$headerName] ) ? $this->headers[$headerName] : null;
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
            case 'requestUri':
                throw new ezcBasePropertyPermissionException( 
                    $propertyName, 
                    ezcBasePropertyPermissionException::READ
                );

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
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
