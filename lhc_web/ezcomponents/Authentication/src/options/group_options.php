<?php
/**
 * File containing the ezcAuthenticationGroupOptions class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class containing the options for group authentication filter.
 *
 * Example of use:
 * <code>
 * $options = new ezcAuthenticationGroupOptions();
 * $options->mode = ezcAuthenticationGroupFilter::MODE_AND;
 * $options->mode->multipleCredentials = false;
 *
 * // $filter1 and $filter2 are authentication filters which need all to succeed
 * // in order for the group to succeed
 * $filter = new ezcAuthenticationGroupFilter( array( $filter1, $filter2 ), $options );
 * </code>
 *
 * @property int $mode
 *           The way of grouping the authentication filters. Possible values:
 *            - ezcAuthenticationGroupFilter::MODE_OR (default): at least one
 *              filter in the group needs to succeed in order for the group to
 *              succeed.
 *            - ezcAuthenticationGroupFilter::MODE_AND: all filters in the group
 *              need to succeed in order for the group to succeed.
 * @property bool $multipleCredentials
 *           If enabled (set to true), each filter must be added to the group
 *           along with a credentials object (through the constructor or with
 *           addFilter()). By default is false (the credentials from the
 *           ezcAuthentication object are used for all filters in the group).
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationGroupOptions extends ezcAuthenticationFilterOptions
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
        $this->mode = ezcAuthenticationGroupFilter::MODE_OR;
        $this->multipleCredentials = false;

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
                $modes = array(
                                ezcAuthenticationGroupFilter::MODE_OR,
                                ezcAuthenticationGroupFilter::MODE_AND
                              );
                if ( !in_array( $value, $modes, true ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ', ', $modes ) );
                }
                $this->properties[$name] = $value;
                break;

            case 'multipleCredentials':
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
