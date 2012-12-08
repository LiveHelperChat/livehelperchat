<?php
/**
 * File containing the ezcWebdavResponse class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class for all response objects.
 *
 * This base class must be extended by all response representation classes.
 *
 * @property int    $status
 *           Response status code.
 * @property string $responseDescription
 *           Optional user readable response description.
 *
 * @version 1.1.4
 * @package Webdav
 */
abstract class ezcWebdavResponse extends ezcWebdavInfrastructureBase
{
    const STATUS_100            = 100;
    const STATUS_101            = 101;

    const STATUS_200            = 200;
    const STATUS_201            = 201;
    const STATUS_202            = 202;
    const STATUS_203            = 203;
    const STATUS_204            = 204;
    const STATUS_205            = 205;
    const STATUS_206            = 206;
    const STATUS_207            = 207;

    const STATUS_300            = 300;
    const STATUS_301            = 301;
    const STATUS_302            = 302;
    const STATUS_303            = 303;
    const STATUS_304            = 304;
    const STATUS_305            = 305;

    const STATUS_400            = 400;
    const STATUS_401            = 401;
    const STATUS_402            = 402;
    const STATUS_403            = 403;
    const STATUS_404            = 404;
    const STATUS_405            = 405;
    const STATUS_406            = 406;
    const STATUS_407            = 407;
    const STATUS_408            = 408;
    const STATUS_409            = 409;
    const STATUS_410            = 410;
    const STATUS_411            = 411;
    const STATUS_412            = 412;
    const STATUS_413            = 413;
    const STATUS_414            = 414;
    const STATUS_415            = 415;
    const STATUS_423            = 423;
    const STATUS_424            = 424;

    const STATUS_500            = 500;
    const STATUS_501            = 501;
    const STATUS_502            = 502;
    const STATUS_503            = 503;
    const STATUS_504            = 504;
    const STATUS_505            = 505;
    const STATUS_507            = 507;

    /**
     * User readable names for error status codes
     * 
     * @var array
     */
    static public $errorNames = array(
        self::STATUS_100        => 'Continue',
        self::STATUS_101        => 'Switching Protocols',

        self::STATUS_200        => 'OK',
        self::STATUS_201        => 'Created',
        self::STATUS_202        => 'Accepted',
        self::STATUS_203        => 'Non-Authoritative Information',
        self::STATUS_204        => 'No Content',
        self::STATUS_205        => 'Reset Content',
        self::STATUS_206        => 'Partial Content',
        self::STATUS_207        => 'Multi-Status',

        self::STATUS_300        => 'Multiple Choices',
        self::STATUS_301        => 'Moved Permanently',
        self::STATUS_302        => 'Moved Temporarily',
        self::STATUS_303        => 'See Other',
        self::STATUS_304        => 'Not Modified',
        self::STATUS_305        => 'Use Proxy',

        self::STATUS_400        => 'Bad Request',
        self::STATUS_401        => 'Unauthorized',
        self::STATUS_402        => 'Payment Required',
        self::STATUS_403        => 'Forbidden',
        self::STATUS_404        => 'Not Found',
        self::STATUS_405        => 'Method Not Allowed',
        self::STATUS_406        => 'Not Acceptable',
        self::STATUS_407        => 'Proxy Authentication Required',
        self::STATUS_408        => 'Request Time-out',
        self::STATUS_409        => 'Conflict',
        self::STATUS_410        => 'Gone',
        self::STATUS_411        => 'Length Required',
        self::STATUS_412        => 'Precondition Failed',
        self::STATUS_413        => 'Request Entity Too Large',
        self::STATUS_414        => 'Request-URI Too Large',
        self::STATUS_415        => 'Unsupported Media Type',
        self::STATUS_423        => 'Locked',
        self::STATUS_424        => 'Failed Dependency',

        self::STATUS_500        => 'Internal Server Error',
        self::STATUS_501        => 'Not Implemented',
        self::STATUS_502        => 'Bad Gateway',
        self::STATUS_503        => 'Service Unavailable',
        self::STATUS_504        => 'Gateway Time-out',
        self::STATUS_505        => 'HTTP Version not supported',
        self::STATUS_507        => 'Insufficient Storage',
    );

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'responseDescription' => null,
    );

    /**
     * Container for header information. 
     * 
     * @var array(string=>mixed)
     */
    protected $headers = array();
    
    /**
     * Construct error response from status.
     * 
     * @param int $status 
     * @return void
     */
    public function __construct( $status = self::STATUS_200 )
    {
        $this->status = $status;
    }

    /**
     * Validates the headers set in this response.
     *
     * This method is called by {@link ezcWebdavServer} after the response object has
     * been created by an {@link ezcWebdavBackend}. It must validate all headers
     * specific for this request for existance of required headers and validity
     * of all headers used  by the specific request implementation. 
     *
     * All extending classes, which overwrite this method, *must* call the
     * parent implementation to ensure that common headers are validated, too!
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
        // set in ezcWebdavTransport::handleResponse()
        if ( !isset( $this->headers['Server'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Server' );
        }
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
    }

    /**
     * Returns the contents of a specific header.
     *
     * Returns the content of the header identified with $headerName and null
     * if no content for the header is available.
     * 
     * @param string $headerName 
     * @return mixed
     */
    public final function getHeader( $headerName )
    {
        return isset( $this->headers[$headerName] ) ? $this->headers[$headerName] : null;
    }

    /**
     * Returns all headers.
     *
     * Returns an array of all headers set in this object, indexed by the
     * header name.
     * 
     * @return array(string=>string)
     */
    public final function getHeaders()
    {
        return $this->headers;
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
            case 'status':
                if ( !isset( self::$errorNames[$propertyValue] ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'HTTP error code' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            case 'responseDescription':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

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

    /**
     * Return valid HTTP response string from error code.
     *
     * The response string will be send as a header to the client, indicating
     * the HTTP version, status code and status message.
     * 
     * @return string
     */
    public function __toString()
    {
        return 'HTTP/1.1 ' . $this->status . ' ' . self::$errorNames[$this->status];
    }
}

?>
