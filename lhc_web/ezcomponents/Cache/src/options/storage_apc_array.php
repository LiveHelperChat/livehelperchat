<?php
/**
 * File containing the ezcCacheStorageFileApcArrayOptions class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Option class for APC array storage.
 *
 * @property int $permissions
 *               File access permissions specified as an octal integer, default 0644.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageFileApcArrayOptions extends ezcCacheStorageApcOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If $options contains a property not defined.
     * @throws ezcBaseValueException
     *         If $options contains a property with a value not allowed.
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->properties['permissions'] = 0644;

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property $name is not defined.
     * @throws ezcBaseValueException
     *         If $value is not correct for the property $name.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case "permissions":
                if ( !is_int( $value ) || $value < 0 || $value > 0777 )
                {
                    throw new ezcBaseValueException( $name, $value, "int > 0 and <= 0777" );
                }
                break;
            default:
                parent::__set( $name, $value );
                return;
        }
        $this->properties[$name] = $value;
    }
}
?>
