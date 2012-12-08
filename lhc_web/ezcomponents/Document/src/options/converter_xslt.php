<?php
/**
 * File containing the ezcDocumentXsltConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentEzp3Xml class
 *
 * @property string $xslt
 *           Path to XSLT, which should be used for the conversion.
 * @property array $parameters
 *           List of aparameters for the XSLT transformation. Parameters are
 *           given as array, with the structure array( 'namespace' => array(
 *           'option' => 'value' ) ), where namespace may also be an empty
 *           string.
 * @property boolean $failOnError
 *           Boolean indicator if the conversion should be aborted, when errors
 *           occurs with an exception, or if the errors just should be ignored.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentXsltConverterOptions extends ezcDocumentConverterOptions
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
        if ( !isset( $this->properties['xslt'] ) )
        {
            $this->properties['xslt'] = null;
        }

        if ( !isset( $this->properties['parameters'] ) )
        {
            $this->parameters = array();
        }

        $this->properties['failOnError'] = false;

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
            case 'xslt':
                $this->properties[$name] = (string) $value;
                break;

            case 'parameters':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array' );
                }

                $this->properties[$name] = $value;
                break;

            case 'failOnError':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'boolean' );
                }

                $this->properties[$name] = (bool) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
