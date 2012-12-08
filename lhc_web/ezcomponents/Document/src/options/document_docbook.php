<?php
/**
 * File containing the ezcDocumentDocbookOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentDocbook class.
 *
 * @property bool $indentXml
 *           Indent XML on output
 * @property string $schema
 *           Path to XML Schema file to validate docbook documents
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookOptions extends ezcDocumentXmlOptions
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
        $this->schema = dirname( __FILE__ ) . '/data/docbook.xsd';

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
            case 'schema':
                if ( !is_file( $value ) || !is_readable( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'XML schema file' );
                }

                $this->properties[$name] = (string) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
