<?php
/**
 * File containing the ezcAuthenticationTypekeyOptions class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class containing the options for the TypeKey authentication filter.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationTypekeyOptions();
 * $options->validity = 60;
 * $options->keysFile = '/tmp/typekey_keys.txt';
 * $options->requestSource = $_POST;
 *
 * // use the options object when creating a new TypeKey filter
 * $filter = new ezcAuthenticationTypekeyFilter( $options );
 *
 * // alternatively, you can set the options to an existing filter
 * $filter = new ezcAuthenticationTypekeyFilter();
 * $filter->setOptions( $options );
 * </code>
 *
 * @property int $validity
 *           The maximum timespan that can exist between the timestamp
 *           sent by the application server at log-in and the timestamp sent
 *           by the TypeKey server. A value of 0 means the validity value
 *           is not taken into consideration when validating the response
 *           sent by the TypeKey server. Do not use a value too small, as
 *           the servers might not be synchronized.
 * @property string $keysFile
 *           The file from where to retrieve the public keys which are used
 *           for checking the TypeKey signature. Can be a local file or a
 *           URL. Default is http://www.typekey.com/extras/regkeys.txt.
 *           Developers can save the file locally once per day to improve the
 *           speed of the TypeKey authentication (which reads this file
 *           at every authentication attempt).
 * @property array(string=>mixed) $requestSource
 *           From where to get the parameters returned by the TypeKey server.
 *           Default is $_GET.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationTypekeyOptions extends ezcAuthenticationFilterOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @throws ezcBaseFileNotFoundException
     *         if the $value file does not exist
     * @throws ezcBaseFilePermissionException
     *         if the $value file cannot be opened for reading
     * @param array(string=>mixed) $options Options for this class
     */
    public function __construct( array $options = array() )
    {
        $this->validity = 0; // seconds
        $this->keysFile = 'http://www.typekey.com/extras/regkeys.txt';
        $this->requestSource = ( $_GET !== null ) ? $_GET : array();

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @throws ezcBaseFileNotFoundException
     *         if the $value file does not exist
     * @throws ezcBaseFilePermissionException
     *         if the $value file cannot be opened for reading
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'validity':
                if ( !is_numeric( $value ) || ( $value < 0 ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int >= 0' );
                }
                $this->properties[$name] = $value;
                break;

            case 'keysFile':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }

                if ( strpos( $value, '://' ) === false )
                {
                    // if $value is not an URL
                    if ( !file_exists( $value ) )
                    {
                        throw new ezcBaseFileNotFoundException( $value );
                    }

                    if ( !is_readable( $value ) )
                    {
                        throw new ezcBaseFilePermissionException( $value, ezcBaseFileException::READ );
                    }
                }
                else
                {
                    // if $value is an URL

                    // hide the notices caused by getaddrinfo (php_network_getaddresses)
                    // in case of unreachable hosts ("Name or service not known")
                    $headers = @get_headers( $value );
                    if ( $headers === false
                         || count( $headers ) === 0 // get_headers returns an empty array for unreachable hosts
                         || strpos( $headers[0], '404 Not Found' ) !== false
                       )
                    {
                        throw new ezcBaseFileNotFoundException( $value );
                    }
                }
                $this->properties[$name] = $value;
                break;

            case 'requestSource':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
