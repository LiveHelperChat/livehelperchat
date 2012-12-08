<?php
/**
 * File containing the ezcMvcRequestParser class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The interface that should be implemented by all request parsers.
 *
 * A request parser takes the raw request - protocol dependent - and creates an
 * abstract ezcMvcRequest object of this.
 *
 * @property  string $prefix The prefix in the URL that should be stripped
 *                           from URL properties.
 *
 * @package MvcTools
 * @version 1.1.3
 */
abstract class ezcMvcRequestParser
{
    /**
     * Contains the request struct
     *
     * @var ezcMvcRequest
     */
    protected $request;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBaseValueException if a the value for a property is out of
     *         range.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            // cases to check for properties
            case 'prefix':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'prefix':
                return $this->properties[$name];
        }
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'prefix':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Constructs a new request parser
     */
    public function __construct()
    {
        $this->properties['prefix'] = '';
    }

    /**
     * Reads the raw request data with what ever means necessary and
     * constructs an ezcMvcRequest object.
     *
     * @return ezcMvcRequest
     */
    abstract public function createRequest();
}
?>
