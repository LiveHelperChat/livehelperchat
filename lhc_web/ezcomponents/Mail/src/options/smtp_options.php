<?php
/**
 * File containing the ezcMailSmtpTransportOptions class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the options for SMTP transport.
 *
 * The options from {@link ezcMailTransportOptions} are inherited.
 *
 * Example of how to use SMTP transport options:
 * <code>
 * $options = new ezcMailSmtpTransportOptions();
 * $options->timeout = 3;
 * $options->connectionType = ezcMailSmtpTransport::CONNECTION_SSL;
 * $options->preferredAuthMethod = ezcMailSmtpTransport::AUTH_NTLM;
 *
 * $smtp = new ezcMailSmtpTransport( 'smtp.example.com', 'user', 'password', null, $options );
 *
 * // the options can also be set via the options property of the SMTP transport:
 * $smtp->options->preferredAuthMethod = ezcMailSmtpTransport::AUTH_NTLM;
 * </code>
 *
 * @property string $connectionType
 *           Specifies the protocol used to connect to the SMTP server. See the
 *           CONNECTION_* constants in the {@link ezcMailSmtpTransport} class.
 * @property array(mixed) $connectionOptions
 *           Specifies additional options for the connection. Must be in this format:
 *           array( 'wrapper_name' => array( 'option_name' => 'value' ) ).
 * @property bool $ssl
 *           This option belongs to {@link ezcMailTransportOptions}, but it is
 *           not used in SMTP.
 *           When trying to set this to true the connectionType option will be set to
 *           {@link ezcMailSmtpTransport::CONNECTION_SSL}.
 *           When trying to set this to false the connectionType option will be set to
 *           {@link ezcMailSmtpTransport::CONNECTION_PLAIN}.
 * @property string $preferredAuthMethod
 *           Specifies which authentication method should be attempted. Default is
 *           null which means that that the transport should try to
 *           authenticate using the methods supported by the SMTP server in their
 *           decreasing strength order. If one method fails an exception will be
 *           thrown. See the AUTH_* constants in the {@link ezcMailSmtpTransport}
 *           class.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailSmtpTransportOptions extends ezcMailTransportOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->connectionType = ezcMailSmtpTransport::CONNECTION_PLAIN; // default is plain connection
        $this->connectionOptions = array(); // default is no extra connection options
        $this->preferredAuthMethod = null; // default is to try the AUTH methods supported by the SMTP server

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'connectionType':
                $this->properties[$name] = $value;
                break;

            case 'connectionOptions':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array' );
                }
                $this->properties[$name] = $value;
                break;

            case 'ssl':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties['connectionType'] = ( $value === true ) ? ezcMailSmtpTransport::CONNECTION_SSL : ezcMailSmtpTransport::CONNECTION_PLAIN;
                break;

            case 'preferredAuthMethod':
                $supportedAuthMethods = ezcMailSmtpTransport::getSupportedAuthMethods();
                $supportedAuthMethods[] = ezcMailSmtpTransport::AUTH_AUTO;
                if ( !in_array( $value, $supportedAuthMethods ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ' | ', $supportedAuthMethods ) );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
