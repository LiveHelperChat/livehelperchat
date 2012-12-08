<?php
/**
 * File containing the ezcMailImapTransportOptions class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the options for IMAP transport.
 *
 * The options from {@link ezcMailTransportOptions} are inherited.
 *
 * Example of how to use IMAP transport options:
 * <code>
 * $options = new ezcMailImapTransportOptions();
 * $options->ssl = true;
 * $options->timeout = 3;
 * $options->uidReferencing = true;
 *
 * $imap = new ezcMailImapTransport( 'imap.example.com', null, $options );
 * </code>
 *
 * @property bool $uidReferencing
 *           Specifies if the IMAP commands will operate with message unique
 *           IDs or with message numbers (default).
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailImapTransportOptions extends ezcMailTransportOptions
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
        $this->uidReferencing = false;

        parent::__construct( $options );
    }

    /**
     * Sets the value of the option $name to $value.
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
            case 'uidReferencing':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
