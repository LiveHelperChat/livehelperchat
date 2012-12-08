<?php
/**
 * File containing the ezcDocumentOdtOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentOdt class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdtOptions extends ezcDocumentXmlOptions
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
        $this->imageDir = sys_get_temp_dir();
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
            case 'imageDir':
                if ( !is_string( $value ) || !is_dir( $value ) || !is_writeable( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'Path to a writeable directory.' );
                }
                break;
            default:
                parent::__set( $name, $value );
        }
        $this->properties[$name] = $value;
    }
}

?>
