<?php
/**
 * File containing the ezcArchiveOptions class
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for ezcBase' autoload.
 *
 * @property bool $readOnly
 *           Whether the archive should be opened in read only mode.
 * @property ezcArchiveCallback $extractCallback
 *           Callback object to be used for every directory and file creation
 *           action, so that permissions, user and group may be changed
 *           depending on user preferences. See {@link ezcArchiveCallback}.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveOptions extends ezcBaseOptions
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
        $this->readOnly = false;
        $this->extractCallback = null;

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
            case 'readOnly':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = $value;
                break;

            case 'extractCallback':
                if ( !is_null( $value ) && !( is_object( $value ) && in_array( 'ezcArchiveCallback', class_parents( $value ) ) ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'instance of ezcArchiveCallback' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}
?>
