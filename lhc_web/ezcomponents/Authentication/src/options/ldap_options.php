<?php
/**
 * File containing the ezcAuthenticationLdapOptions class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class containing the options for ldap authentication filter.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationLdapOptions();
 * $options->protocol = ezcAuthenticationLdapFilter::PROTOCOL_TLS;
 *
 * // use the options object when creating a new LDAP filter
 * $ldap = new ezcAuthenticationLdapInfo( 'localhost', 'uid=%id%', 'dc=example,dc=com', 389 );
 * $filter = new ezcAuthenticationLdapFilter( $ldap, $options );
 *
 * // alternatively, you can set the options to an existing filter
 * $filter = new ezcAuthenticationLdapFilter( $ldap );
 * $filter->setOptions( $options );
 * </code>
 *
 * @property int $protocol
 *           How to connect to the LDAP server:
 *            - ezcAuthenticationLdapFilter::PROTOCOL_PLAIN - plain connection
 *            - ezcAuthenticationLdapFilter::PROTOCOL_TLS   - TLS connection
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationLdapOptions extends ezcAuthenticationFilterOptions
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
        $this->protocol = ezcAuthenticationLdapFilter::PROTOCOL_PLAIN;

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
            case 'protocol':
                $allowedValues = array(
                                        ezcAuthenticationLdapFilter::PROTOCOL_PLAIN,
                                        ezcAuthenticationLdapFilter::PROTOCOL_TLS
                                      );
                if ( !in_array( $value, $allowedValues, true ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ', ', $allowedValues ) );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
