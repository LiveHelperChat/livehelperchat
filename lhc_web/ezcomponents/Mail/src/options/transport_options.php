<?php
/**
 * File containing the ezcMailTransportOption class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for mail transports.
 *
 * @property int $timeout
 *           Specifies the time in seconds until the connection is closed if
 *           there is no activity through the connection.
 * @property bool $ssl
 *           Specifies whether to use an SSL connection or not.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailTransportOptions extends ezcBaseOptions
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
        $this->timeout = 5; // default value for timeout is 5 seconds
        $this->ssl = false; // default value for ssl is false

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
            case 'timeout':
                if ( !is_numeric( $value ) || ( $value < 1 ) ) 
                {
                    throw new ezcBaseValueException( $name, $value, 'int >= 1' );
                }
                $this->properties[$name] = (int) $value;
                break;

            case 'ssl':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}
?>
