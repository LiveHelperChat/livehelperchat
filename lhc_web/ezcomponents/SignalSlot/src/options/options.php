<?php
/**
 * File containing the ezcConsoleStatusbarOptions class.
 *
 * @package SignalSlot
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleOutput class.
 * This class stores the options for the {@link ezcSignalCollection} class.
 *
 * @property array(string) $signals
 *           The signals that the signal collection can throw. If this option
 *           is set using a non-existent signal is considered an exceptional state.
 *           If this option is not set or is set to null then using a non existent
 *           signal is simply ignored.
 *
 * @package SignalSlot
 * @version 1.1.1
 */
class ezcSignalCollectionOptions extends ezcBaseOptions
{
    /**
     * Construct a new options object.
     * Options are constructed from an option array by default. The constructor
     * automatically passes the given options to the __set() method to set them
     * in the class.
     *
     * @param array(string=>mixed) $options The initial options to set.
     * @return void
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     */
    public function __construct( array $options = array() )
    {
        $this->properties['signals'] = null;
        parent::__construct( $options );
    }

    /**
     * Option write access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseSettingValueException
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
            case "signals":
                if ( $value != null && !is_array( $value ) )
                {
                    throw new ezcBaseSettingValueException( $key, $value, 'null, array(string)' );
                }
                break;
            default:
                throw new ezcBaseSettingNotFoundException( $key );
        }
        $this->properties[$key] = $value;
    }
}

?>
