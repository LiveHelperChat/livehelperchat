<?php
/**
 * File containing the ezcAuthenticationOpenidOptions class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class containing the options for the OpenID authentication filter.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationOpenidOptions();
 * $options->mode = ezcAuthenticationOpenidFilter::MODE_SMART;
 * $options->store = new ezcAuthenticationOpenidFileStore( '/tmp/store' );
 * $options->timeout = 5;
 * $options->timeoutOpen = 3;
 * $options->requestSource = $_POST;
 * $options->immediate = true;
 * $options->returnUrl = 'http://example.com';
 * $options->openidVersion = ezcAuthenticationOpenidFilter::VERSION_2_0;
 *
 * // use the options object when creating a new OpenID filter
 * $filter = new ezcAuthenticationOpenidFilter( $options );
 *
 * // alternatively, you can set the options to an existing filter
 * $filter = new ezcAuthenticationSession();
 * $filter->setOptions( $options );
 * </code>
 *
 * @property int $mode
 *           The OpenID mode to use for authentication. It is either dumb
 *           (ezcAuthenticationOpenidFilter::MODE_DUMB, default) or smart
 *           (ezcAuthenticationOpenidFilter::MODE_SMART). In dumb mode
 *           the OpenID server does most of the work, but an extra check
 *           is required (check_authentication step). In smart mode the
 *           server and the OpenIP provider establish a shared secret (with
 *           an expiry period) that is used to sign the responses, so the
 *           check_authentication step is not required.
 * @property ezcAuthenticationOpenidStore $store
 *           The store to use to hold the nonces and (for MODE_SMART) the
 *           associations between the server and the OpenID provider. Default
 *           is null which means nonces are not used. If you enable MODE_SMART
 *           you have to specify also a valid store.
 * @property string $nonceKey
 *           The query key that identifies the nonce value, default 'nonce'.
 * @property int $nonceLength
 *           The length of the generated nonces, default 6.
 * @property int $nonceValidity
 *           The amount of seconds the nonces are allowed to be valid.
 * @property int $timeout
 *           The amount of seconds allowed as timeout for fetching content
 *           during HTML or Yadis discovery.
 * @property int $timeoutOpen
 *           The amount of seconds allowed as timeout when creating a connection
 *           with fsockopen() for the HTML or Yadis discovery.
 * @property array(string=>mixed) $requestSource
 *           From where to get the parameters returned by the OpenID provider.
 *           Default is $_GET.
 * @property bool $immediate
 *           Enables OpenID checkid_immediate instead of checkid_setup. See the
 *           ezcAuthenticationOpenidFilter class documentation for more details.
 *           It is false by default (use checkid_setup by default).
 * @property string $returnUrl
 *           URL to return to after the successful authentication by the
 *           OpenID provider. Default value is null, in which case the OpenID
 *           provider will return to the current URL (the URL that initiated
 *           the authentication, from HTTP_HOST + REQUEST_URI server variables).
 * @property string $openidVersion
 *           Which OpenID protocol version to try. Default is "1.1". Other
 *           possible values are "1.0" and "2.0".
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationOpenidOptions extends ezcAuthenticationFilterOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options Options for this class
     */
    public function __construct( array $options = array() )
    {
        $this->mode = ezcAuthenticationOpenidFilter::MODE_DUMB; // stateless mode
        $this->store = null;
        $this->nonceKey = 'nonce';
        $this->nonceLength = 6; // characters
        $this->nonceValidity = 24 * 60 * 60; // seconds
        $this->timeout = 3; // seconds
        $this->timeoutOpen = 3; // seconds
        $this->requestSource = ( $_GET !== null ) ? $_GET : array();
        $this->immediate = false;
        $this->returnUrl = null; // default = return to the currently called URL
        $this->openidVersion = ezcAuthenticationOpenidFilter::VERSION_1_1;

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'mode':
                $allowedValues = array(
                                        ezcAuthenticationOpenidFilter::MODE_DUMB,
                                        ezcAuthenticationOpenidFilter::MODE_SMART
                                      );
                if ( !in_array( $value, $allowedValues, true ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ', ', $allowedValues ) );
                }
                $this->properties[$name] = $value;
                break;

            case 'store':
                if ( $value !== null && !$value instanceof ezcAuthenticationOpenidStore )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationOpenidStore || null' );
                }
                $this->properties[$name] = $value;
                break;

            case 'nonceKey':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            case 'nonceLength':
            case 'nonceValidity':
            case 'timeout':
            case 'timeoutOpen':
                if ( !is_numeric( $value ) || ( $value < 1 ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int >= 1' );
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

            case 'immediate':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = $value;
                break;

            case 'returnUrl':
                if ( !is_string( $value ) && !is_null( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            case 'openidVersion':
                if ( !is_string( $value ) && !is_null( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
