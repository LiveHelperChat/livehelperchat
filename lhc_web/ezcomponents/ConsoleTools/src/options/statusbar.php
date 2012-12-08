<?php
/**
 * File containing the ezcConsoleStatusbarOptions class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleOutput class.
 * This class stores the options for the {@link ezcConsoleOutput} class.
 *
 * @property string $successChar
 *           The char shown for a succeeded status.
 * @property string $failureChar
 *           The char shown for a failed status.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleStatusbarOptions extends ezcBaseOptions
{
    protected $properties = array(
        'successChar' => "+",
        'failureChar' => "-",
    );

    /**
     * Option write access.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseValueException
     *         If a desired property value is out of range.
     *
     * @param string $key Name of the property.
     * @param mixed $value  The value for the property.
     * @ignore
     */
    public function __set( $key, $value )
    {
        switch ( $key )
        {
            case "successChar":
            case "failureChar":
                if ( is_string( $value ) === false || strlen( $value ) < 1 )
                {
                    throw new ezcBaseValueException( $key, $value, 'string, not empty' );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
        }
        $this->properties[$key] = $value;
    }
}

?>
