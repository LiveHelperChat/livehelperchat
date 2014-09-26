<?php
/**
 * File containing the ezcAuthenticationHtpasswdOptions class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class containing the options for the htpasswd authentication filter.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationHtpasswdOptions();
 * $options->plain = true;
 *
 * // use the options object when creating a new htpasswd filter
 * $filter = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd', $options );
 *
 * // alternatively, you can set the options to an existing filter
 * $filter = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd' );
 * $filter->setOptions( $options );
 * </code>
 *
 * @property bool $plain
 *           Specifies if the password is passed to the filter in plain
 *           text or encrypted. The encryption will be autodetected by the
 *           filter from the password stored in the htpasswd file.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationHtpasswdOptions extends ezcAuthenticationFilterOptions
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
        $this->plain = false;

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
            case 'plain':
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
